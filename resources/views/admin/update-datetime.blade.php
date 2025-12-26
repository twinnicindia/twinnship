<form method="post" action="{{url('submit-update-datetime-rr')}}" enctype="multipart/form-data">
    @csrf
    <input type="file" name="importFile">
    <br>
    <input type="submit" value="Submit">
</form>
