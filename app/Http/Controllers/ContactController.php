<?php

use Illuminate\Http\Request;
use Mail;


class ContactController extends Controller
{
    public function create()
    {
        return view('contact');
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
            $message->to('tu_email@example.com', 'Tu Nombre')
                    ->subject('Nuevo Mensaje de Contacto');
        });

        return redirect()->route('contact.create')->with('success', 'Mensaje enviado correctamente.');
    }
}