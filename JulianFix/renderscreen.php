	<?php 
	
	static function renderScreen ($screenID) {
		self::init();
		
		//Set the screen ID
		$formulize_screen_id = $screenID;

		//Declare a formulize div to contain our injected content, with ID formulize_form
		echo '<div id=formulize_form>';
		
		//Include our header file in order to set up xoTheme
		include XOOPS_ROOT_PATH . '/header.php';
		
		//If we have a xoTheme, then we will be able to dupe the Formulize system into thinking we are in icms, in order
		//to set up an icmsTheme object. The icmsTheme object is required by a number of elements that should work in 3rd
		//party sites (i.e. datebox). We thus mimic what occurs in icms and set up our theme object accordingly.
		if($xoTheme)
		{
			global $icmsTheme;
			$icmsTheme = $xoTheme;
		}
		
		//We buffer our output of HTML injection. This prevents the buffer from being printed before we have printed and loaded our
		//JS scripts to the page.
		ob_start;
		include XOOPS_ROOT_PATH . '/modules/formulize/index.php';
		//Content now contains our buffered contents.
		$content = ob_get_clean();
		
		//Checks icmsTheme is initialized. If this is so, it will drop into further conditionals to check those
		//dependencies relying on library JS files from Formulize stand-alone directory.
		if($icmsTheme)
		{
			//If this global is set, then we are requiring a date-box element. In that case we shall add the following
			//scripts to our page load, in order for the calendar to achieve functionality.
			if(isset($GLOBALS['formulize_calendarFileRequired']))
			{	
				foreach($GLOBALS['formulize_calendarFileRequired']['scripts'] as $thisScript) {
                                       echo "<script type='text/javascript' src='" . $thisScript . "'></script>";
                }
				
				echo "<script type='text/javascript'>".$GLOBALS['formulize_calendarFileRequired']['src'][0]."</script>";
				
				//In order to append our stylesheet, and ensure that no matter the load and buffer order of our page, we shall be including
				//the style sheet via a JS call that appends the link tag to the head section on load.
				echo
				"
					<script type='text/javascript'>
					function fetchCalendarCSS(fileURL)
					{
						var newNode=document.createElement('link');
						newNode.setAttribute('rel', 'stylesheet');
						newNode.setAttribute('type', 'text/css');
						newNode.setAttribute('href', fileURL);
						document.getElementsByTagName('head')[0].appendChild(newNode);
					}";
					foreach($GLOBALS['formulize_calendarFileRequired']['stylesheets'] as $thisSheet) {
						print " fetchCalendarCSS('" . $thisSheet ."'); ";
					}
					print "</script>";
			}
		}
		//Inject formulize content
		echo $content;
		//Close our div tag
		echo '</div>';
	}