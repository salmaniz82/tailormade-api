<?php class Template {


	public $template = false;
	public $hasData = false;
	public $hasPage = false;

	
	public function load($page, $data=null)
	{
		if(strpos($page, '.') === false)
    	{
    		$page = $page.'.php';
    		
    	}
    	else {
    		$page = $page;
    	}

        return require_once ABSPATH . 'pages/'.$page;
	}


	public function render($template, $data)
	{

		$path = ABSPATH .'pages/'. $template;

		
		if(file_exists($path))
		{

			$contents = file_get_contents($path);	
			foreach($data as $key => $value)
			{
				
				if(!is_array($value))
				{
					$contents = preg_replace('/\{'.$key.'\}/', $value, $contents);	
				}
				
			}

			

			eval('?> '. $contents  .' <?php ');
		}
		else {
			echo 'Template file not found';
		}

		return $this;

	}

	public function view()
	{
		
		return $this;
	}


	public function layout($template)
	{
		$path = ABSPATH .'pages/templates/'. $template;

		
		if(file_exists($path))
		{

			$masterTemplate = file_get_contents($path);	

			$this->template = $masterTemplate;
			
		}
		else {
			echo 'Master Layout Template file not found ' . $path;
		}

		return $this;
	}

	public function compile($page, $data)
	{
		

		$path = ABSPATH .'pages/'. $page;

		if(file_exists($path))
		{

			$masterTemplate  = $this->template;
			$pageContents = file_get_contents($path);

			$unifyTemplate = preg_replace('/\{{'.'contents'.'\}}/', $pageContents, $masterTemplate);


			foreach($data as $key => $value)
			{
				
				if(!is_array($value))
				{
					$unifyTemplate = preg_replace('/\{'.$key.'\}/', $value, $unifyTemplate);	
				}
				
			}

			

			eval('?> '. $unifyTemplate .' <?php ');


			
		}
		else {
			echo 'Template file not found' . $path;
		}

		return $this;

	}

	

	

}


