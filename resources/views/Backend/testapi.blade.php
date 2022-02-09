@extends('Backend.master')
@section('title', 'TEST axios')
@section('content')
    <div>
        <input type="text" name="token" id="token" value="">
        <button id="req">請求</button>
        <button id="login">登入</button>

    </div>

@endsection
@section('js')
    <script>
        $("#req").click(function() {

            var token = $('#token').val();
            console.log(`Bearer ${token}`) ; 
            // var url = "http://pc.laravel.uec/" ; 
            var url = "https://uecbackend.u-ark.com/" ; 

            const config = {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            };
            const bodyParameters = {
                // key: "value"
            };

            axios.get(
                url +'api/member/orders?date=2021-12-19',
                    config
                ).then((res) => {
                    console.log(res.headers) ;
                    console.log(res.headers.authorization);
                    // console.table(res.data)
                })
                .catch((error) => {
                    console.error(error)
                })
                .finally(() => {
                    /* 不論失敗成功皆會執行 */
                })
        });

        $("#login").click(function() {
            // var url = "http://pc.laravel.uec/" ; 
            var url = "https://uecbackend.u-ark.com/" ; 
            var token = $('#token').val();
            const config = {};
            const bodyParameters = {
                mobile: "0999000128",
                password:"Taco0224",
            };

            axios.post(
                    url + 'api/members/login',
                    bodyParameters,
                    config
                ).then((res) => {
                    console.log('Authenticated');

                    $('#token').val(res.data.result._token) ;
                })
                .catch((error) => {
                    console.error(error)
                })
                .finally(() => {
                    /* 不論失敗成功皆會執行 */
                })
        });
    </script>
@endsection
