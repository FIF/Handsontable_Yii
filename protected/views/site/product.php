<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="language" content="en">

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/handsontable.full.min.js"></script>
	<link rel="stylesheet" media="screen" href="<?php echo Yii::app()->request->baseUrl; ?>/css/handsontable.full.min.css">
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

	<style>
	.pad {
	    margin: auto;
	    width: 60%;
	    border:3px solid #8AC007;
	    padding: 10px;
	}
	</style>
</head>

<body>
<?php 
  print_r($products); 
  print_r($this->products); 
?>

<div id='content'>

    <div class="pad" data-jsfiddle="example1">
      <h2>PHP example</h2>

      <p>
        <button name="load">Load</button>
        <button name="save">Save</button>
        <button name="reset">Reset</button>
        <!-- <label><input type="checkbox" name="autosave" checked="checked" autocomplete="off"> Autosave</label> -->
      </p>

      <div id="exampleConsole" class="console">Click "Load" to load data from server</div>

      <div id="example1"></div>

      <p>
        <button name="dump" data-dump="#example1" data-instance="hot" title="Prints current data source to Firebug/Chrome Dev Tools">
          Dump data to console
        </button>
      </p>
    </div>

<script type="text/javascript" charset="utf-8">

$(document).ready(function () {


var
  $container = $("#example1"),
  $console = $("#exampleConsole"),
  $parent = $container.parent(),
  autosaveNotification,
  hot;

hot = new Handsontable($container[0], {
  columnSorting: true,
  startRows: 8,
  startCols: 5,
  rowHeaders: true,
  colHeaders: ['Product', 'Manufacturer', 'Year', 'Import Price', 'VN Price'],
  columns: [
    {},
    {},
    {},
    {},
    {},

  ],
  minSpareCols: 0,
  minSpareRows: 1,
  contextMenu: true,
  afterChange: function (change, source) {
    var data;

    if (source === 'loadData' || !$parent.find('input[name=autosave]').is(':checked')) {
      return;
    }
    data = change[0];

    // transform sorted row to original row
    data[0] = hot.sortIndex[data[0]] ? hot.sortIndex[data[0]][0] : data[0];

    clearTimeout(autosaveNotification);
    $.ajax({
      url: "<?= Yii::app()->request->baseUrl?>/product/update",
      dataType: 'json',
      type: 'POST',
      data: {changes: change}, // contains changed cells' data
      success: function () {
        $console.text('Autosaved (' + change.length + ' cell' + (change.length > 1 ? 's' : '') + ')');

        autosaveNotification = setTimeout(function () {
          $console.text('Changes will be autosaved');
        }, 1000);
      }
    });
  }
});

$parent.find('button[name=load]').click(function () {
  $.ajax({
    url: "<?= Yii::app()->request->baseUrl?>/product/load",
    dataType: 'json',
    type: 'GET',
    success: function (res) {
      var data = [], row;
/*      console.log(res.cars);*/

      $console.text('Data loaded');
      hot.loadData(data);
    }
  });
}).click(); // execute immediately

$parent.find('button[name=save]').click(function () {
  $.ajax({
    url: "<?= Yii::app()->request->baseUrl?>/product/update",
    data: {data: hot.getData()}, // returns all cells' data
    dataType: 'json',
    type: 'POST',
    success: function (res) {
      if (res.result === 'ok') {
        $console.text('Data saved');
      }
      else {
        $console.text('Save error');
      }
    },
    error: function () {
      $console.text('Save error');
    }
  });
});

$parent.find('input[name=autosave]').click(function () {
  if ($(this).is(':checked')) {
    $console.text('Changes will be autosaved');
  }
  else {
    $console.text('Changes will not be autosaved');
  }
});



});

</script>

</body>
</html>
