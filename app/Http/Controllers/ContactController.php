<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class ContactController extends Controller
{
    public function create()
    {
        return view('web.contact');
    }

    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        // Enviar el correo electrónico
        Mail::send('emails.contact', ['data' => $request->all()], function($message) use ($request) {
            $message->to('info@fudounar.org', 'fudounar')
                    ->subject('Nuevo Mensaje de Contacto');
        });

        return redirect()->route('contact.create')->with('success', 'Mensaje enviado correctamente.');
    }
}