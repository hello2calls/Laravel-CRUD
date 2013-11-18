
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Bootstrap, from Twitter</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->
        <link href="assets/css/bootstrap.css" rel="stylesheet">
        <link href="assets/css/docs.css" rel="stylesheet">
        <link href="assets/js/google-code-prettify/prettify.css" rel="stylesheet">
        <style>
            body {
                padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
            }
        </style>
        <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="assets/js/html5shiv.js"></script>
        <![endif]-->

        <!-- Fav and touch icons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">
        <link rel="shortcut icon" href="assets/ico/favicon.png">
    </head>

    <body>

        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="brand" href="#">Project name</a>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="span9">
                <?php
                $module_name = $_POST['module_name'];
                $data = $_POST['field_name'];
                $sql = ' CREATE TABLE  ' . $module_name . '  ( <br>';
                $sql .= ' id int(11) NOT NULL, <br>'; 
                $fields = array();
                foreach ($data as $key => $value):
                    $field = "     ";
                    $field .= $_POST['field_name'][$key] . ' ';
                    $field .= $_POST['field_type'][$key] . '(';
                    $field .= $_POST['field_length'] [$key] . ')' . '';
                    array_push($fields, $field);
                endforeach;
                $sql .= implode($fields, ', <br>');
                $sql .= ' <br> created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, <br>'; 
                $sql .= " updated_at timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', <br>";
                $sql .=  " PRIMARY KEY (`id`) <br>";
                $sql .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
                ?>
                <div class="bs-docs-example">
<pre><?php echo $sql; ?></pre>
                </div>
                
            </div>
            <div class="span9">
                <div class="bs-docs-example">
<pre>
class <?php echo ucfirst($module_name); ?> extends Eloquent {

protected $table = '<?php echo $module_name; ?>s';

}
</pre>
                </div>
            </div>
            <div class="span9">
                <?php
                $module_name = $_POST['module_name'];
                $data = $_POST['field_name'];
                $sql = ' CREATE TABLE  ' . $module_name . '  ( <br>';
                $fields = array();
                foreach ($data as $key => $value):
                    $field = "     ";
                    $field .= $_POST['field_name'][$key] . ' ';
                    $field .= $_POST['field_type'][$key] . '(';
                    $field .= $_POST['field_length'] [$key] . ')' . '';
                    array_push($fields, $field);
                endforeach;
                $sql .= implode($fields, ', <br>');
                $sql .= '<br> )';
                ?>
                <div class="bs-docs-example">
<pre>                        
class <?php echo ucfirst($module_name); ?>Controller extends BaseController { 

protected $layout = 'layouts/manage-main';

<br>
// Default Action   
public function getIndex() {

if (!Auth::check())
return Redirect::to('login')->with('message', 'Login Failed');
$<?php echo $module_name; ?> = DB::table('<?php echo $module_name; ?>s')->paginate(2);

View::share('<?php echo $module_name; ?>s', $<?php echo $module_name; ?>);
$this->layout->content = View::make('<?php echo $module_name; ?>/<?php echo $module_name; ?>_list');
}
<br> 
// Add Action
public function getCreate() {

if (!Auth::check())
return Redirect::to('login')->with('message', 'Login Failed');

$this->layout->content = View::make('<?php echo $module_name; ?>/<?php echo $module_name; ?>_add');
}  

<br> 
// Store ( Save add ) Action
public function postStore() {

    if (!Auth::check())
    return Redirect::to('login')->with('message', 'Login Failed');

    $input = Input::get();
    $rules = array(
    <?php foreach ($data as $key => $value): ?>
    '<?php echo $_POST['field_name'][$key]; ?>' => 'required',
    <?php endforeach; ?>
    );

    $validator = Validator::make($input, $rules);
    if ($validator->passes()) {
        $<?php echo $module_name; ?> = new <?php echo ucfirst($module_name); ?>();
    <?php foreach ($data as $key => $value): ?>
        $<?php echo $module_name; ?>-><?php echo $_POST['field_name'][$key]; ?> = Input::get('<?php echo $_POST['field_name'][$key]; ?>', '');                                
    <?php endforeach; ?>
        $<?php echo $module_name; ?>->save();
        return Redirect::to('<?php echo $module_name; ?>');
    } else {
        $messages = $validator->messages();
        return Redirect::back()->withInput()->withErrors($messages);
    }
}

<br>
// Show Details
public function getShow($id) {
if (!Auth::check())
return Redirect::to('login')->with('message', 'Login Failed');
if (!$id)
return 'Error!';

$<?php echo $module_name; ?> = <?php echo ucfirst($module_name); ?>::find($id);
if (!$<?php echo $module_name; ?>)
return 'Error!';
$<?php echo $module_name; ?>_data = <?php echo ucfirst($module_name); ?>::find($id)->toArray();
View::share('<?php echo $module_name; ?>', $<?php echo $module_name; ?>_data);
$this->layout->content = View::make('<?php echo $module_name; ?>/<?php echo $module_name; ?>_details');
}

<br>
// Show Edit
public function getEdit($id) {
if (!Auth::check())
return Redirect::to('login')->with('message', 'Login Failed');
if (!$id)
return 'Error!';

$<?php echo $module_name; ?> = <?php echo ucfirst($module_name); ?>::find($id);
if (!$<?php echo $module_name; ?>)
return 'Error!';
$<?php echo $module_name; ?>_data = <?php echo ucfirst($module_name); ?>::find($id)->toArray();
View::share('<?php echo $module_name; ?>', $<?php echo $module_name; ?>_data);
$this->layout->content = View::make('<?php echo $module_name; ?>/<?php echo $module_name; ?>_edit');
}

<br>
// Save Edit
public function putUpdate($id) {
if (!Auth::check())
return Redirect::to('login')->with('message', 'Login Failed');
$<?php echo $module_name; ?>=<?php echo ucfirst($module_name); ?>::find($id);
<?php foreach ($data as $key => $value): ?>
$<?php echo $module_name; ?>-><?php echo $_POST['field_name'][$key]; ?> = Input::get('<?php echo $_POST['field_name'][$key]; ?>', '');                                
<?php endforeach; ?>
$<?php echo $module_name; ?>->save();
return Redirect::to('<?php echo $module_name; ?>');
}

<br>
// Delete
public function deleteDestroy($id) {
if (!Auth::check())
return Redirect::to('login')->with('message', 'Login Failed');
return 'DELETE';
}


}
</pre>
                </div>
            </div>


            <div class="span9">
                <div class="bs-docs-example">
<pre>

@section('content') 
@show
</pre>
                </div>
            </div>

<div class="span9">
<div class="bs-docs-example">
<pre>
@section('content')
foreach ($<?php echo $module_name; ?>s as $<?php echo $module_name; ?>): 
<?php foreach ($data as $key => $value): ?>
echo $<?php echo $module_name; ?>-><?php echo $_POST['field_name'][$key]; ?>;                                
<?php endforeach; ?>       
endforeach;

// Details Link -- URL::to('<?php echo $module_name; ?>/show/'.$content->id)
// Edit Link -- URL::to('<?php echo $module_name; ?>/edit/'.$content->id.'/edit')
//Delete Link -- URL::to('<?php echo $module_name; ?>/'.$content->id) 

// Print Pagination        
echo $<?php echo $module_name; ?>s->links();
@endsection

</pre>
</div>
</div>

            
<!-- Add View Starts -->
<div class="span9">
<div class="bs-docs-example">
<?php 
    $input = array( 'button',
                    'checkbox',
                    'color',
                    'date',
                    'datetime',
                    'datetime-local',
                    'email',
                    'file',
                    'hidden',
                    'image',
                    'month',
                    'number',
                    'password',
                    'radio',
                    'range',
                    'reset',
                    'search',
                    'submit',
                    'tel',
                    'text',
                    'time',
                    'url',
                    'week'
            );
?>    
<pre>
@section('content')
{{ Form::open(array('url' => '<?php echo $module_name; ?>/store', 'method' => 'post')) }}


<?php foreach ($_POST['input_type'] as $key => $value): ?>
@if ($errors->first('<?php echo $_POST['field_name'][$key];?>'))
&#60;div class="alert alert-error"&#62;{{ $errors->first('<?php echo $_POST['field_name'][$key];?>') }}&#60;/div&#62;
@endif
<?php   if($value == 'text'){ ?>  
{{Form::text('<?php echo $_POST['field_name'][$key];?>', Input::old('<?php echo $_POST['field_name'][$key];?>'))}}
<?php } ?> 
<?php   if($value == 'textarea'){ ?>  
{{ Form::textarea('<?php echo $_POST['field_name'][$key];?>', Input::old('<?php echo $_POST['field_name'][$key];?>'),array('class' => 'span12','cols'=>'5','rows'=>'28')) }} 
<?php } ?>
<?php   if($value == 'select'){ ?>  
{{Form::select('<?php echo $_POST['field_name'][$key];?>', array(0 => 'Option 1', 1 => 'Option 2', 2 => 'Option 3', 3 => 'Option 4'), Input::old('<?php echo $_POST['field_name'][$key];?>'), array('class' => 'styled'))}} 
<?php } ?>



<?php endforeach; ?>     
@endsection
</pre>
</div>
</div>
<!-- Add View Ends -->


<!-- Edit View Starts -->
<div class="span9">
<div class="bs-docs-example">
<pre>

</pre>
</div>
</div>
<!-- Edit View Ends -->


        </div> <!-- /container -->

        <script src="assets/js/jquery.js"></script>
        <script type="text/javascript">
            function add_more(){
                $("#tbl").append($("#scratch").clone());
            }

            function create(){}
        </script>

    </body>
</html>


