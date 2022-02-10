@extends('backend.master')
@section('title', 'TEST axios')
@section('content')
    <div>
        <input type="text" name="token" id="token" value="">
        <button id="req">請求</button>
        <button id="login">登入</button>
        <button id="testheader">測試取得Header</button>
        <button id="reqmid">測試經過中介取得Header</button>


    </div>

@endsection
@section('js')
    <script>
        // var url = "http://pc.laravel.uec/" ; 
        var url = "https://uecbackend.u-ark.com/";

        $("#req").click(function() {

            var token = $('#token').val();
            // console.log(`Bearer ${token}`) ; 
            console.log(`Bearer ${token}`) ;

            const config = {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            };
            const bodyParameters = {
                // key: "value"
            };

            axios.get(
                    url + 'api/member/orders?date=2021-12-19',
                    config
                ).then((res) => {
                    console.table(res.headers);
                    // console.log(res.data.headers);
                    console.table(res.data)
                })
                .catch((error) => {
                    console.error(error)
                })
                .finally(() => {
                    /* 不論失敗成功皆會執行 */
                })
        });

        $("#login").click(function() {
            var token = $('#token').val();
            const config = {};
            const bodyParameters = {
                mobile: "0999000128",
                password: "Taco0224",
            };

            axios.post(
                    url + 'api/members/login',
                    bodyParameters,
                    config
                ).then((res) => {
                    console.log('登入');
                    $('#token').val(res.data.result._token);
                })
                .catch((error) => {
                    console.error(error)
                })
                .finally(() => {
                    /* 不論失敗成功皆會執行 */
                })
        });

        $("#testheader").click(function() {
            var token = $('#token').val();
            const config = {};
            const bodyParameters = {
                // mobile: "0999000128",
                // password:"Taco0224",
            };

            axios.get(
                    url + 'api/testheaderauthorization',
                    bodyParameters,
                    config
                ).then((res) => {
                    console.table(res.headers);
                })
                .catch((error) => {
                    console.error(error)
                })
                .finally(() => {
                    /* 不論失敗成功皆會執行 */
                })
        });

        $("#reqmid").click(function() {

            var token = $('#token').val();
            // console.log(`Bearer ${token}`) ; 

            const config = {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            };
            const bodyParameters = {
                // key: "value"
            };

            axios.get(
                    url + 'api/mid/testheaderauthorization',
                    config
                ).then((res) => {
                    console.table(res.headers);
                    console.table(res.data)
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
