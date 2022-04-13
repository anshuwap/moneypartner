<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div>
        <form id="test-payment-form" class="form-inline" method="Post" action="https://payin.merchantpe.in/ws/api/authValidate">
            <input type="hidden" id="id" name="id" value="{{$key}}"><br>
            <input type="hidden" id="transactionID" name="transactionID" value="{{$transaction_id}}"><br>
            <input type="hidden" id="amount" name="amount" value="{{$amount}}"><br>
            <input type="hidden" name="hash" id="hash" value="{{$hash}}"><br>
            <input style="display: none;" id="form-submit" type="submit" class="form-control" name="submit" value="Submit"><br>
        </form>

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#form-submit').trigger('click');
          	//$($('#id').val() . '<br>' . $('#transactionID').val() . '<br>' . $('#amount').val() . '<br>' . $('#hash').val());
        });
    </script>
</body>

</html>