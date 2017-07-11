<?php 
namespace wkapi\components;

use yii\base\Widget;

/**
* 
*/
class MyWidget extends Widget
{
	
	function init()
	{
		ob_start();
	}

	public function run()
	{
		$content = ob_get_clean();
		return "{$content}
		<code class='hljs xl'>
			<span class='hljs title'>title</span>
			<span class='hljs-function'>aaaaa</pre>
		</code>
		";
	}
}

?>