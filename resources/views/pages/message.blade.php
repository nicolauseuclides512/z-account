@extends('layouts.app-nonavbar')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="col-lg-12">

                    <div class="panel panel-default">
                        {{--<div class="panel-heading"></div>--}}

                        <div class="panel-body">
                            @if(session("message"))
                                {{ session("message")}}

                                <br/>
                                <a href="{{ session("loginUrl") }}" target="_self">login.</a>

                            @else
                                Oops nothing.
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection