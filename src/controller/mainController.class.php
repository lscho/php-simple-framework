<?php
	class mainController extends baseController{
		function indexAction(){
			$as='hello';
			$hello='as';
			echo $$hello;
		}
	}