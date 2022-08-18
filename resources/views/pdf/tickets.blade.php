<!DOCTYPE html>
<html>
<head>
<style>
    #table{
         width: 100%
    }
    #table, th ,td {
        border: 1px solid #dee2e6;
        border-collapse: collapse;
        font-family: "Nunito", sans-serif;
        font-size: 0.9rem;
        color: #676463;
    }
</style>
</head>
<body>
<br>
<br>
<table id="table">
    <thead>
    <tr style="text-align: left">
        <th>Empresa</th>
        <th>Descripción</th>
        <th>Placa</th>
        <th>Fecha Ingreso</th>
        <th>Fecha Egreso</th>
        <th>Min. Transcurridos</th>
        <th>Hrs. Transcurridas</th>
    </tr>
    </thead>
    <tbody>
    @foreach($tickets as $ticket)
    <tr style="text-align: left">
        <td>{{$ticket->empresa ?? ''}}</td>
        <td>{{$ticket->desccripcion}}</td>
        <td>{{$ticket->placa}}</td>
        <td>{{$ticket->fecha_ingreso}}</td>
        <td>{{$ticket->fecha_egreso}}</td>
        <td>{{$ticket->minutos }}  min.</td>
        <td>{{$ticket->horas}} hrs.</td>
    </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
