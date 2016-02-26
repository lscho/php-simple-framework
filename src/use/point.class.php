<?php
	class point  extends Uses{
        function notify($data){
            $behavior=strtolower($data['behavior']);
            switch ($behavior) {
            	case 'index':
            		echo 111;
            		break;
            }
        }		
	}