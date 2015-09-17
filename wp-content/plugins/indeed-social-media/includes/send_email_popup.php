<div class="popup_wrapp" id="popup_box">
    <div class="the_popup">
            <div class="popup_top">
                <div class="title"><?php echo get_option("email_box_title");?></div>
                <div class="close_bttn" onClick="closePopup();"></div>
                <div class="clear"></div>
            </div>
            <div class="popup_content">
               <div class="popup_form">
                <div class="popup_name">
                  <input type="text" id="ism_email_name" placeholder="Your Name" />
				</div> 
				<div class="popup_from"> 
		          <input type="text" id="ism_email_from" placeholder=": From Email"/>
				</div>
				<div class="popup_sendto"> 
                  <input type="text" id="ism_email_sentto" placeholder=": To Email(s)"/>
				</div> 
				<div class="popup-label"> 
                   Subject:       
                 </div>
				 <div class="popup_subject">       
				 <?php
                  $subject = stripslashes( get_option('email_subject') );
                  if(strpos($subject, '#LINK#')) $subject = str_replace('#LINK#', $_REQUEST['permalink'] , $subject);
                  ?>
                  <input type="text" id="ism_email_subject" value="<?php echo $subject;?>"/>
                </div>
				<div class="popup-label">    
					Message:
				</div>	
                <div class="popup_message">         
                        <?php
                            $message = stripslashes( get_option("email_message") );
                            if(strpos($message, '#LINK#')) $message = str_replace('#LINK#', $_REQUEST['permalink'] , $message);
                        ?>
                        <textarea id="ism_email_message"><?php echo $message;?></textarea>
                </div>
                <?php if( get_option('email_capcha')==1){ ?>
                 <div class="popup-label">    
					Answer:
				</div>	
				 <div class="popup_captcha">       
					<?php $rand = rand(91,95);	?>
					<input type="text" value="" id="capcha_answer" placeholder="What is the result for <?php echo ism_capcha_q( $rand );?> ?"/>
					<input type="hidden" value="<?php echo $rand;?>" id="cp_ar_k"/>
                </div>        
               <?php } ?>
                    <input type="hidden" value="<?php echo get_option('email_success_message');?>" id="email_success_message"/>
                    <input type="hidden" value="<?php echo $_REQUEST['permalink'];?>" id="popup_ism_theurl" />
               </div>
                <div class="the_button" onClick="ism_func_sendEmail();">Send</div>
				<div class="clear"></div>
                <div class="loading" id="loading_img"></div>
            </div>
    </div>
</div>
