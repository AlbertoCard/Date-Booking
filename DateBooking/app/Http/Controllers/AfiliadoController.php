<?php

namespace App\Http\Controllers;
use App\Models\Usuario;
use App\Models\Establecimiento;
use App\Models\EstabXusuario;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AfiliadoController extends Controller
{

    //Obtiene todos los afiliados del establecimiento
    public function getAfiliadosByEstablecimiento($uid){

        $usuarioEstablecimiento = EstabXusuario::where('id_usuario', $uid)
            ->first();
        
        $id_establecimiento = $usuarioEstablecimiento->id_establecimiento;

        $establecimiento = Establecimiento::findOrFail($id_establecimiento);
        $afiliados = $establecimiento->usuarios()->where('rol', 'afiliado')->get();

        return response()->json($afiliados);

    }

    public function agregarAfiliado(Request $request){
        
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
            return response()->json(['error' => 'No se encontró un establecimiento asociado al usuario.'], 404);
        }

        $id_establecimiento = $establecimiento->id_establecimiento;

        //Busca el usuario por el correo. Si no existe, lo manda al registro a registro.
        $usuario = Usuario::where('email', $email)
            ->first();

        if(!$usuario){
            return response()->json(['redirect' => true, 'message' => 'El correo no ha sido registrado, ¿desea añadirlo?'], 200);
        }

        if($usuario->rol == 'establecimiento'){
            return response()->json(['error' => 'No puedes añadir un establecimiento como afiliado.'], 403);
        }

        //Verifica si el correo ya ha sido agregado como afiliado del establecimiento.
        $yaAfiliado = EstabXusuario::where('id_usuario', $usuario->uid)
            ->where('id_establecimiento', $id_establecimiento)
            ->first();

        if($yaAfiliado){
            return response()->json(['error' => 'El usuario ya esta registrado como afiliado del establecimiento.'], 409);
        }

        //Cambia el rol del usuario a "afiliado".
        $usuario->rol = 'afiliado';
        $usuario->save();

        //Crea la relación en la tabla estbXusuario.
        EstabXusuario::create([
            'id_usuario' => $usuario->uid,
            'id_establecimiento' => $id_establecimiento
        ]);

        return response()->json(['message' => 'El usuario ha sido afiliado con exito.']);
    }


    //Elimina un afiliado
    public function dropAfiliados($uid, $id_establecimiento){
        $afiliadoEliminar = Usuario::where('uid', $uid)
            ->first();

        $relacion = EstabXusuario::where('id_usuario', $uid)
            ->where('id_establecimiento', $id_establecimiento)
            ->first();

        if($relacion){
            $relacion->delete();
        }

        $afiliadoEliminar->delete();

        return response()->json(['message' => 'Afiliado eliminado con exito.']);
    }

    //Plus, desafilia al afiliado (y lo convierte en cliente).
    public function dropRelacionAfiliado($uid, $id_establecimiento){

        $usuarioEstablecimiento = EstabXusuario::where('id_usuario', $uid)
            ->first();
        
        $id_establecimiento = $usuarioEstablecimiento->id_establecimiento;

        $afiliado = Usuario::where('uid', $uid)
            ->first();

        $relacion = EstabXusuario::where('id_usuario', $uid)
            ->where('id_establecimiento', $id_establecimiento)
            ->first();

        // Elimina la relación con el establecimiento actual
        if($relacion){
            EstabXusuario::where('id_usuario', $uid)
            ->where('id_establecimiento', $id_establecimiento)
            ->delete();
        }

        $masRelaciones = EstabXusuario::where('id_usuario', $uid)
            ->first();

        if(!$masRelaciones){
            $afiliado->rol = 'cliente';
            $afiliado->save();
        }
        
        return response()->json(['message' => 'Afiliado ha sido desasociado con exito.'], 200);
    }
}