<?php
/*
Plugin Name: Farmacias Portuguesas Plugin
Plugin URI: http://farmaciasdeservico.net/
Description: O plugin Farmacias Portuguesas adiciona um widget ao teu blog que mostra as farmacias de serviço em Portugal. Pode ser integrado em qualquer sitio no do teu site.
Version: 1.0
Author: Ricardo Mendes
Author URI: http://ricardo-mendes.com
License: GPL3
*/

function farmacia($options, $flagService, $count){
	$url = 'http://www.farmaciasdeservico.net/';
	//$urlCurl = $url .'/localidade/'.$options["district"].'/'.$options["city"].'/expedientes';
	$urlCurl = '';
	if ($flagService){
		$urlCurl = $url . 'localidade/'.$options["district"].'/'.$options["city"];
	} else{
		$urlCurl = $url . 'localidade/'.$options["district"].'/'.$options["city"].'/expedientes';
	}
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $urlCurl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$html = curl_exec($ch);
	curl_close($ch);

	# Create a DOM parser object
	$dom = new DOMDocument();

	# Parse the HTML from Google.
	# The @ before the method call suppresses any warnings that
	# loadHTML might throw because of invalid HTML in the page.
	$dom->loadHTML($html);

	$xpath = new DOMXpath($dom);
	echo '<ul>';
	$result = $xpath->query('//div[@class="farmacia"]/div[@class="iconFarmacia"]');
	foreach($result as $link) {
		if($count++<$options['count']){
			$arr = $xpath->query("./strong/a",$link);
			echo '<li>';
		  foreach($arr as $item) {
				$href =  $item->getAttribute("href");
				echo '<a href="'.$url . '' . $item->getAttribute("href").'">';
				echo '<strong>';
				echo $item->nodeValue;
				if ($flagService){
					echo ' (de Serviço)';
				}
				echo '</strong>';
				echo '</a>';
		  }
			$arr = $xpath->query("./p", $link);
			foreach($arr as $item) {
				echo '<br>' . $item->nodeValue;
			}
			echo '</li>';
		}
	}
	echo '</ul>';
	return $count;
}

function farmaciasdeservico(){
	$options = get_option("widget_farmaciasdeservico");
	if (!is_array($options)){
		$options = array(
			'district' => 'aveiro',
			'city' => 'ilhavo',
      'title' => 'Farmácias de Serviço',
			'count' => '5'
    );
  }
	$count = 0;
	$count = farmacia($options, true, $count);
	$count = farmacia($options, false, $count);

}

function widget_farmaciasdeservico($args){
	extract($args);
  $options = get_option("widget_farmaciasdeservico");
  if (!is_array($options)){
    $options = array(
			'district' => 'aveiro',
			'city' => 'ilhavo',
			'title' => 'Farmácias de Serviço',
			'count' => '5'
    );
  }

  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  farmaciasdeservico();
  echo $after_widget;
}

function farmaciasdeservico_control(){
  $options = get_option("widget_farmaciasdeservico");
  if (!is_array($options)){
    $options = array(
			'district' => 'aveiro',
			'city' => 'ilhavo',
      'title' => 'Farmácias de Serviço',
			'count' => '5'
    );
  }

  if($_POST['farmaciasdeservico-Submit']){
		$options['district'] = htmlspecialchars($_POST['farmaciasdeservico-WidgetDistrict']);
    $options['city'] = htmlspecialchars($_POST['farmaciasdeservico-WidgetCity']);
    $options['title'] = htmlspecialchars($_POST['farmaciasdeservico-WidgetTitle']);
		$options['count'] = htmlspecialchars($_POST['farmaciasdeservico-WidgetCount']);
    update_option("widget_farmaciasdeservico", $options);
  }
?>
  <p>
		<label for="farmaciasdeservico-WidgetDistrict">District: </label>
    <input type="text" id="farmaciasdeservico-WidgetDistrict" name="farmaciasdeservico-WidgetDistrict" value="<?php echo $options['District'];?>" />
    <br /><br />
		<label for="farmaciasdeservico-WidgetCity">City: </label>
    <input type="text" id="farmaciasdeservico-WidgetCity" name="farmaciasdeservico-WidgetCity" value="<?php echo $options['city'];?>" />
    <br /><br />
    <label for="farmaciasdeservico-WidgetTitle">Widget Title: </label>
    <input type="text" id="farmaciasdeservico-WidgetTitle" name="farmaciasdeservico-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
		<label for="farmaciasdeservico-WidgetTitle">Widget Count: </label>
    <input type="text" id="farmaciasdeservico-WidgetCount" name="farmaciasdeservico-WidgetCount" value="<?php echo $options['count'];?>" />
    <br /><br />
    <input type="hidden" id="farmaciasdeservico-Submit"  name="farmaciasdeservico-Submit" value="1" />
  </p>
<?php
}

function farmaciasdeservico_init(){
	wp_register_sidebar_widget('farmaciasdeservico','Farmacias de Servico', 'widget_farmaciasdeservico');
  	wp_register_widget_control('farmaciasdeservico','Farmacias de Servico', 'farmaciasdeservico_control', 300, 200);
}
add_action("plugins_loaded", "farmaciasdeservico_init");
?>
