<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Establecimiento;
use App\Models\EstabXusuario;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AfiliadoController extends Controller
{

    //Obtiene todos los afiliados del establecimiento
    public function getAfiliadosByEstablecimiento($uid)
    {

        $usuarioEstablecimiento = EstabXusuario::where('id_usuario', $uid)
            ->first();

        if (!$usuarioEstablecimiento) {
            Log::error("No se encontró relación EstabXusuario para usuario: $uid");
            return response()->json(['error' => 'No se encontró relación con establecimiento.'], 404);
        }

        $id_establecimiento = $usuarioEstablecimiento->id_establecimiento;

        $establecimiento = Establecimiento::find($id_establecimiento);
        if (!$establecimiento) {
            Log::error("No se encontró establecimiento con id: $id_establecimiento");
            return response()->json(['error' => 'No se encontró el establecimiento.'], 404);
        }
        $afiliados = $establecimiento->usuarios()->where('rol', 'afiliado')->get();

        return response()->json($afiliados);
    }

    public function agregarAfiliado(Request $request)
    {

        Log::info('UID recibido: ' . $request->input('uid'));
        Log::info('Email recibido: ' . $request->input('email'));

        $validatedData = $request->validate([
            'email' => 'required|email',
            'uid' => 'required|string',
        ]);

        $email = $validatedData['email'];
        $uid = $validatedData['uid'];

        $establecimiento = EstabXusuario::where('id_usuario', $uid)
            ->first();

        if (!$establecimiento) {
            Log::error("No se encontró un establecimiento asociado al usuario: $uid");
            return response()->json(['error' => 'No se encontró un establecimiento asociado al usuario.'], 404);
        }

        $id_establecimiento = $establecimiento->id_establecimiento;

        //Busca el usuario por el correo. Si no existe, lo manda al registro a registro.
        $usuario = Usuario::where('email', $email)
            ->first();

        if (!$usuario) {
            Log::warning("El correo $email no ha sido registrado.");
            return response()->json(['redirect' => true, 'message' => 'El correo no ha sido registrado, ¿desea añadirlo?'], 200);
        }

        if ($usuario->rol == 'establecimiento') {
            Log::warning("Intento de añadir un establecimiento ($email) como afiliado.");
            return response()->json(['error' => 'No puedes añadir un establecimiento como afiliado.'], 403);
        }

        //Verifica si el correo ya ha sido agregado como afiliado del establecimiento.
        $yaAfiliado = EstabXusuario::where('id_usuario', $usuario->uid)
            ->where('id_establecimiento', $id_establecimiento)
            ->first();

        if ($yaAfiliado) {
            Log::warning("El usuario $email ya está registrado como afiliado del establecimiento $id_establecimiento.");
            return response()->json(['error' => 'El usuario ya esta registrado como afiliado del establecimiento.'], 409);
        }

        DB::beginTransaction();

        try {
            // Cambia el rol del usuario a "afiliado".
            $usuario->rol = 'afiliado';
            $usuario->save();

            // Crea la relación en la tabla estbXusuario.
            EstabXusuario::create([
                'id_usuario' => $usuario->uid,
                'id_establecimiento' => $id_establecimiento
            ]);

            DB::commit();
            return response()->json(['message' => 'El usuario ha sido afiliado con exito.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al afiliar usuario: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al afiliar el usuario.'], 500);
        }
    }


    //Elimina un afiliado
    public function dropAfiliados($uid, $id_userEstablecimiento)
    {

        $usuarioEstablecimiento = EstabXusuario::where('id_usuario', $id_userEstablecimiento)
            ->first();

        if (!$usuarioEstablecimiento) {
            Log::error("No se encontró relación EstabXusuario para usuario: $id_userEstablecimiento");
            return response()->json(['error' => 'No se encontró relación con establecimiento.'], 404);
        }

        $id_establecimiento = $usuarioEstablecimiento->id_establecimiento;

        $afiliadoEliminar = Usuario::where('uid', $uid)
            ->first();

        if (!$afiliadoEliminar) {
            Log::error("No se encontró usuario afiliado con uid: $uid");
            return response()->json(['error' => 'No se encontró el afiliado.'], 404);
        }

        $relacion = EstabXusuario::where('id_usuario', $uid)
            ->where('id_establecimiento', $id_establecimiento)
            ->first();

        DB::beginTransaction();

        try {
            if ($relacion) {
                EstabXusuario::where('id_usuario', $uid)
                    ->where('id_establecimiento', $id_establecimiento)
                    ->delete();
            } else {
                Log::warning("No se encontró relación para eliminar entre usuario $uid y establecimiento $id_establecimiento");
            }

            $afiliadoEliminar->delete();

            DB::commit();
            return response()->json(['message' => 'Afiliado eliminado con exito.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar afiliado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar afiliado.'], 500);
        }
    }

    //Plus, desafilia al afiliado (y lo convierte en cliente).
    public function dropRelacionAfiliado($uid, $id_userEstablecimiento)
    {

        $usuarioEstablecimiento = EstabXusuario::where('id_usuario', $id_userEstablecimiento)
            ->first();

        if (!$usuarioEstablecimiento) {
            Log::error("No se encontró relación EstabXusuario para usuario: $id_userEstablecimiento");
            return response()->json(['error' => 'No se encontró relación con establecimiento.'], 404);
        }

        $id_establecimiento = $usuarioEstablecimiento->id_establecimiento;

        $afiliado = Usuario::where('uid', $uid)
            ->first();

        if (!$afiliado) {
            Log::error("No se encontró usuario afiliado con uid: $uid");
            return response()->json(['error' => 'No se encontró el afiliado.'], 404);
        }

        $relacion = EstabXusuario::where('id_usuario', $uid)
            ->where('id_establecimiento', $id_establecimiento)
            ->first();

        DB::beginTransaction();

        try {
            // Elimina la relación con el establecimiento actual
            if ($relacion) {
                EstabXusuario::where('id_usuario', $uid)
                    ->where('id_establecimiento', $id_establecimiento)
                    ->delete();
            } else {
                Log::warning("No se encontró relación para eliminar entre usuario $uid y establecimiento $id_establecimiento");
            }

            $masRelaciones = EstabXusuario::where('id_usuario', $uid)
                ->first();

            if (!$masRelaciones) {
                $afiliado->rol = 'cliente';
                $afiliado->save();
            }

            DB::commit();
            return response()->json(['message' => 'Afiliado ha sido desasociado con exito.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar afiliado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar afiliado.'], 500);
        }
    }
}
