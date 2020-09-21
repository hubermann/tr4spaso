@extends('layouts.backend')

@section('content')
<!--row -->
<div class="row">
    <div class="col-sm-12">
		<div class="white-box">
            <h3>Datos para recibir transferencias bancarias.</h3>
            <p>Banco: <span><b>{{ $datos->name }}{{ $datos->id}}</b></span></p>
            <p>Email para informar pago: <span><b>{{ $datos->email }}</b></span></p>
            <p>Alias: <span><b>{{ $datos->alias }}</b></span></p>
            <p>CBU: <span><b>{{ $datos->cbu }}</b></span></p>

        </div>
        
        <p><a href="{{ route('backend.transfers_data.edit', ['id' => $datos->id])}}" class=" btn btn-primary pull-right text-right" >Modificar <i class="fa fa-pencil"></i></a></p>
    </div>
</div>
<!-- /.row -->

@endsection