
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
        <link href="assets/css/tomorrow-night.css" rel="stylesheet">
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
                $sql = ' CREATE TABLE  ' . $module_name . 's( <br>';
                $sql .= "\tid INT(11) NOT NULL AUTO_INCREMENT, <br>"; 
                $fields = array();
				$field_keys=array();
                foreach ($data as $key => $value):
                    $field = "\t";
                    $field .= $_POST['field_name'][$key] . ' ';
                    $field .= $_POST['field_type'][$key].' ';
					if(!empty($_POST['field_length'] [$key]))
						$field .= '('.$_POST['field_length'] [$key] . ') ';
					$field .= (isset($_POST['field_null'] [$key]))?'NULL':'NOT NULL ';
					$field .= ($_POST['field_default'] [$key])?'DEFULT '.$_POST['field_default'] [$key].' ':'';
					//$field .= (isset($_POST['field_inc'] [$key]))?'AUTO_INCREMENT ':'';
                    array_push($fields, $field);
                endforeach;
                $sql .= implode($fields, ', <br>');
                $sql .= "<br>\tcreated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, <br>"; 
                $sql .= "\tupdated_at timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', <br>";
                $sql .=  "\tPRIMARY KEY (id) <br>";
                $sql .= ')ENGINE=InnoDB DEFAULT CHARSET=utf8;';
                ?>
                <div class="bs-docs-example">
				MySql
<pre><?php echo $sql; ?></code></pre>
                </div>				
                
            </div>
            <div class="span9">
                <div class="bs-docs-example">
				Model
<pre><code data-language="php">
class <?php echo str_replace(' ','',ucwords(str_replace('_',' ',$module_name))); ?> extends Eloquent {

	protected $table = '<?php echo $module_name; ?>s';

}
	</code></pre>
					</div>
				</div>
				<div class="span9">
	<div class="bs-docs-example">
	Controller
	<pre><code data-language="php">                       
class <?php echo str_replace(' ','',ucwords(str_replace('_',' ',$module_name))); ?>Controller extends BaseController { 

	protected $layout = 'layouts/console';
	protected $layout_ajax = 'layouts/console_ajax';

	// Default Action   
	public function getIndex() {
		$<?php echo $module_name; ?>s = <?php echo str_replace(' ','',ucwords(str_replace('_',' ',$module_name))); ?>->paginate(2);

		View::share('<?php echo $module_name; ?>s', $<?php echo $module_name; ?>s);
		$this->layout->content = View::make('<?php echo $module_name; ?>/index');
	}
	 
	// Add Action
	public function getSave($id=NULL) {
		if($id){
			$<?php echo $module_name; ?>=<?php echo str_replace(' ','',ucwords(str_replace('_',' ',$module_name))); ?>::find($id);
			View::share('<?php echo $module_name; ?>', $<?php echo $module_name; ?>);
		}
		$this->layout->content = View::make('<?php echo $module_name; ?>/add');		
	}  

	 
	// Store ( Save add ) Action
	public function postSave() {
		$input = Input::get();
		$rules = array(
		<?php foreach ($data as $key => $value):
			$rule=array();
			if(isset($_POST['required'][$key]))
				array_push($rule,'required');
			if($_POST['input_type'][$key]=='tel' || $_POST['input_type'][$key]=='number')
				array_push($rule,'number');
			if($_POST['input_type'][$key]=='email')
				array_push($rule,'email');
			if($_POST['input_type'][$key]=='url')
				array_push($rule,'url');
			if($_POST['input_type'][$key]=='date')
				array_push($rule,'date');
			
		?>
		'<?php echo $_POST['field_name'][$key]; ?>' => '<?php echo implode('|',$rule);?>',
		<?php endforeach; ?>
	);

		$validator = Validator::make($input, $rules);
		if ($validator->passes()) {
	<?php echo "\t\t$".$module_name; ?> = new <?php echo str_replace(' ','',ucwords(str_replace('_',' ',$module_name))); ?>();
	<?php foreach ($data as $key => $value): ?>
	<?php echo "\t$".$module_name; ?>-><?php echo $_POST['field_name'][$key]; ?> = Input::get('<?php echo $_POST['field_name'][$key]; ?>', '');
	<?php endforeach; ?>
	<?php echo "\t$".$module_name; ?>->save();
			return Redirect::to('<?php echo str_replace('_','-',$module_name); ?>');
		} else {
			$messages = $validator->messages();
			return Redirect::back()->withInput()->withErrors($messages);
		}
	}


	// Show Details
	public function getDetails($id) {
		if (!$id) return 'Error!';
		$<?php echo $module_name; ?> = <?php echo str_replace(' ','',ucwords(str_replace('_',' ',$module_name))); ?>::find($id);
		if (!$<?php echo $module_name; ?>) return 'Error!';
		View::share('<?php echo $module_name; ?>', $<?php echo $module_name; ?>);
		$this->layout->content = View::make('<?php echo $module_name; ?>/details');
	}


	// Show Edit
	public function putSave($id) {
		if (!$id) return 'Error!';
		$<?php echo $module_name; ?> = <?php echo str_replace(' ','',ucwords(str_replace('_',' ',$module_name))); ?>::find($id);
		if (!$<?php echo $module_name; ?>) return 'Error!';
		View::share('<?php echo $module_name; ?>', $<?php echo $module_name; ?>);
		$this->layout->content = View::make('<?php echo $module_name; ?>/edit');
	}

	// Delete
	public function deleteRemove() {
		$id = Input::get('id');
		$<?php echo $module_name; ?>=<?php echo str_replace(' ','',ucwords(str_replace('_',' ',$module_name))); ?>::find($id);
		$<?php echo $module_name; ?>->delete();
	}


}
</code></pre>
                </div>
            </div>


            <div class="span9">
                <div class="bs-docs-example">
<pre><code data-language="php">   
@section('content') 
@show
</code></pre>
                </div>
            </div>

<div class="span9">
<div class="bs-docs-example">
View: List
<pre><code data-language="php"> 
@section('content')
@section('title')
School Automation
@endsection
@section('style')
@endsection
@section('script')
@endsection
@section('content')
<section class="vbox">
    <header class="header bg-white b-b">
        <p><?php echo $module_name; ?></p>
    </header>
    <section class="scrollable wrapper">
    <div class="row">
        <div class="col-lg-12">
		@foreach ($<?php echo $module_name; ?>s as $<?php echo $module_name; ?>): 
		<?php foreach ($data as $key => $value): ?>
		@echo $<?php echo $module_name; ?>-><?php echo $_POST['field_name'][$key]; ?>;                                
		<?php endforeach; ?>       
		@endforeach;

		// Details Link -- URL::to('<?php echo $module_name; ?>/details/'.$content->id)
		// Edit Link -- URL::to('<?php echo $module_name; ?>/save/'.$content->id)
		// Delete Link -- URL::to('<?php echo $module_name; ?>/remove'.$content->id) 

		// Print Pagination        
		{{$<?php echo $module_name; ?>s->links();}}
		@endsection
        </div>
    </div>
    </section>
</section>
@endsection
</code></pre>
</div>
</div>

            
<!-- Add View Starts -->
<div class="span9">
<div class="bs-docs-example">
View: Save/Edit
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
<pre><code data-language="php"> 
@section('title')
School Automation
@endsection
@section('style')
<style type="text/css">
</style>
@endsection
@section('script')
@endsection
@section('content')
<section class="vbox">
    <header class="header bg-white b-b">
        <p>Dashboard</p>
    </header>
    <section class="scrollable wrapper">
    <div class="row">
        <div class="col-lg-12">
		{{ Form::open(array('url' => '<?php echo str_replace('_','-',$module_name); ?>/save', 'method' => isset($<?php echo $module_name; ?>)?'put':'post')) }}
		<?php foreach ($_POST['input_type'] as $key => $value): ?>
		<?php   if($value == 'text'||$value == 'tel'||$value == 'url'||$value == 'date'){ ?>  
		{{Form::<? echo $value;?>('<?php echo $_POST['field_name'][$key];?>', Input::old('<?php echo $_POST['field_name'][$key];?>',isset($<?php echo $module_name; ?>-><?php echo $_POST['field_name'][$key];?>)?$<?php echo $module_name; ?>-><?php echo $_POST['field_name'][$key];?>:''))}}
		<?php } ?> 
		<?php   if($value == 'textarea'){ ?>  
		{{ Form::textarea('<?php echo $_POST['field_name'][$key];?>', Input::old('<?php echo $_POST['field_name'][$key];?>',isset($<?php echo $module_name; ?>-><?php echo $_POST['field_name'][$key];?>)?$<?php echo $module_name; ?>-><?php echo $_POST['field_name'][$key];?>:''),array('class' => 'span12','cols'=>'5','rows'=>'28')) }}
		<?php } ?>
		<?php   if($value == 'select'){ ?>  
		{{Form::select('<?php echo $_POST['field_name'][$key];?>', array(0 => 'Option 1', 1 => 'Option 2', 2 => 'Option 3', 3 => 'Option 4'), Input::old('<?php echo $_POST['field_name'][$key];?>',isset($<?php echo $module_name; ?>-><?php echo $_POST['field_name'][$key];?>)?$<?php echo $module_name; ?>-><?php echo $_POST['field_name'][$key];?>:''), array('class' => 'styled'))}}
		<?php } ?>
		@if ($errors->first('<?php echo $_POST['field_name'][$key];?>'))
		&#60;div class="alert alert-error"&#62;{{ $errors->first('<?php echo $_POST['field_name'][$key];?>') }}&#60;/div&#62;
		@endif
		<?php endforeach; ?> 
		{{Form::hidden('id',isset($<?php echo $module_name; ?>->id)?$<?php echo $module_name; ?>->id:'')}}
		{{Form::submit('Submit');}} 
		{{Form::close()}} 
        </div>
    </div>
    </section>
</section>
@endsection	  
</code></pre>
</div>
</div>
<!-- Add View Ends -->


<!-- Edit View Starts -->
<div class="span9">
<div class="bs-docs-example">
<pre><code data-language="php"> 

</code></pre>
</div>
</div>
<!-- Edit View Ends -->


        </div> <!-- /container -->

        <script src="assets/js/jquery.js"></script>
		<script src="assets/js/rainbow-custom.min.js"></script>
		
        <script type="text/javascript">
            function add_more(){
                $("#tbl").append($("#scratch").clone());
            }

            function create(){}
        </script>

    </body>
</html>