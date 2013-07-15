<?php
########################################
#This  comes with absolutley NO WARRENTY and I assume NO RESPONSABILITY if this does any damage to any #system running it.
#
#This code is released under gnu.
#
#By [NICK] http://www.linickx.com
#
#Further info about this project/code might be avilable at http://www.linickx.com
#
####################################
#
#Copyright: 2005
#
#This program is free software; you can redistribute it and/or modify 
#it under the terms of the GNU General Public License as published by 
#the Free Software Foundation; either version 2 of the License, or 
#(at your option) any later version
#
#This program is distributed in the hope that it will be useful, but
#WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
#General Public License for more details.
#
#You should have received a copy of the GNU General Public License with
#this program.  If not, write to the Free Software Foundation, Inc., 675
#Mass Ave, Cambridge, MA 02139, USA.
#
# or http://www.linickx.com (I will always keep a copy there !)
#########################################################################


# CUSTOM Functions to do silly stuff in theme.

function themehacker_ip() {
	$srcip = $_SERVER["REMOTE_ADDR"] ;
	$now = date(U);
	?><div class="ip">Remote IP <?php echo $srcip;?> Recorded at <?php echo $now;?></div>
	<?php
}

function themehacker_browser() {
	$browser =  $_SERVER["HTTP_USER_AGENT"];
	?><div class="browser">Browser Details Recored: <?php echo $browser;?></div>
	<?php
}
?>
