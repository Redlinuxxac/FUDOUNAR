<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Mensaje de Contacto</title>
</head>
<body>
    <p>Nombre: {{ $data['name'] }}</p>
    <p>Email: {{ $data['email'] }}</p>
    <p>Mensaje:</p>
    <p>{{ $data['message'] }}</p>
</body>
</html>