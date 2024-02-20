<div>
<script src="//code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/gauge.js/1.2.1/gauge.min.js" integrity="sha512-CvDF0JVxliK2VV8gGA7qEEyRPcORRA2miPvpDhXvlfw0TpbGAmoQHMmEP2eziwKLsNz8PaoNfs4yjnlcpn4E3w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<!--  	<link href="//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
<link href="/css/results.css" rel="stylesheet">

<x-filament::card>
    <x-filament::card.heading>
        @if($record->finishedScanning())
            Scan completed
        @else
            <div class="flex items-center">
                <svg class="animate-spin mr-4 h-5 w-5 dark:text-white text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Scanning...
            </div>
        @endif
    </x-filament::card.heading>

    <div>
        <ul class="space-y-2">
            <li>
                Sender: <u>{{ $record->sender?->email_address ?? '(deleted sender)' }}</u>
            </li>
            <li>
                Keyword: <u>{{ $record->keyword }}</u>
            </li>
        </ul>
    </div>
</x-filament::card>




     <?php 
     $newsletterScore = $record->processSpamScore();
     $color = '';
     
     $hasMailtester = false;
     $mailTestJson = false;
     if($record->has_mail_tester == 1) {
         $mailTestJson = $record->getMailtesterData();
      
         if(isset($mailTestJson['status']) && $mailTestJson['status']) {
             $hasMailtester = true;
         }
     }
     
     if($hasMailtester) {
         
         
         $mailtestScore = 10 + $mailTestJson['mark'];
         
         //var_dump($mailtestScore);
         
         $currentScore = $mailTestJson['spamAssassin']['score'];
         
          
         if($currentScore >= 5) {
             $spamScoreVal = 'Do not send';
             $color = '#FF0000'; //red
         }else if($currentScore > 2 && $currentScore < 5 ) {
             $spamScoreVal = 'Send with Caution';
             $color = '#ffc800'; //orange
         }else if($currentScore > 0 && $currentScore < 2 ) {
             $spamScoreVal = 'Good';
             $color = '#0000FF'; //blue
             
         }else if($currentScore   < 0 ) {
             $spamScoreVal = 'Excellent';
             $color = '#7FFF00'; //green
             
         } 
         
         if($mailtestScore <= 2.5) {
           $spamScoreVal = 'Do not send';
           $color = '#FF0000'; //red
         }else if($mailtestScore > 2.5 && $mailtestScore <= 5 ) {
           $spamScoreVal = 'Send with Caution';
           $color = '#ffc800'; //orange
         }else if($mailtestScore > 5 && $mailtestScore < 7.5 ) {
           $spamScoreVal = 'Good';
           $color = '#0000FF'; //blue
           
         }else if($mailtestScore   >= 7.5 ) {
          
           $spamScoreVal = 'Excellent';
           $color = '#7FFF00'; //green
         }
         
     ?>
     
<!--      <div> -->
<?php 
/*
 <iframe class='tester' src="https://www.mail-tester.com/<?php echo $record->getMailTesterIdentifier()?>"></iframe>
 */
 ?>
     	 
     	 <!--      </div> -->
      
	
	<div style="text-align: center; margin-top: 10px; margin-bottom: 10px;"> 
         <x-filament::button type="button" class="spam-analysis analysis btn-active">
              Spam Analysis
        </x-filament::button>
        
         <x-filament::button type="button" class="email-analysis analysis">
            Email Analysis
        </x-filament::button>
    	 
	</div>
	<div class="email-analysis-section" style="display: none;">
		<div class="filament-widgets-container grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-8 mb-6">
    		<div class="filament-widget col-span-1">
    			<div class="accordion" id="mailtest-errors">
            			<?php 
            			$accordionTitle = "body_errors";
            			?>
            			      <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $accordionTitle?>" aria-expanded="false" aria-controls="<?php echo $accordionTitle?>">
                                    <?php echo $mailTestJson['body']['title'];?>
                                    <i class="status <?php echo $mailTestJson['body']['statusClass']?>"></i>
                                  </button>
                                </h2>
                                <div id="<?php echo $accordionTitle?>" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#mailtest-errors">
                                  <div class="accordion-body">
                                    <?php echo $mailTestJson['body']['description']?>
                                    <hr> 
                                  		<?php echo $mailTestJson['body']['messages']?>
                                  	 
                                  	 
                                  	  <div class="accordion" id="body_errors_data">
                                            <?php foreach($mailTestJson['body']['subtests'] as $titleName => $testInfo) {
                                                if($titleName == 'textToHtmlRatio')
                                                    continue;
                                                
                                                $accordionTitle = "body_errors_subtest_".$titleName;
                                             ?>
                                              <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingOne-1">
                                                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $accordionTitle?>" aria-expanded="true" aria-controls="<?php echo $accordionTitle?>">
                                                    <?php echo $testInfo['title']?>
                                                    <i class="status <?php echo $testInfo['statusClass']?>"></i>
                                                  </button>
                                                </h2>
                                                <div id="<?php echo $accordionTitle?>" class="accordion-collapse collapse" aria-labelledby="headingOne-1" data-bs-parent="#body_errors_data">
                                                  <div class="accordion-body test-result">
        											<?php echo $testInfo['description']?>
        											<hr>
        											 <div class='result'>
        											<?php echo $testInfo['messages']?> 
        											</div>
                                                  </div>
                                                </div>
                                              </div>
                                             
                                             <?php 
                                         }?>
                                     </div>
                                     
                                  </div>
                                </div>
                              </div> 
                        </div>
    		</div>
    		<div class="filament-widget col-span-1">
    				 <iframe class='tester' src="/view-html.php?id=<?php echo $record->getMailTesterIdentifier()?>"></iframe>
    				
    		</div>
		</div>
	</div>
	<div class="spam-analysis-section">
	     <div class="filament-widgets-container grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-8 mb-6" style="margin-top: 20px;">
    
    
    		<div  
    			class="filament-widget col-span-1 filament-widgets-chart-widget">
    			<div
    				class="p-2 space-y-2 bg-white rounded-xl shadow dark:border-gray-600 dark:bg-gray-800">
    
    
    				<div class="space-y-2">
    					<div class="px-4 py-2 space-y-4">
    						<div class="flex items-center justify-between gap-8">
    							<h2
    								class="text-xl font-semibold tracking-tight filament-card-heading">
    								Mailyser Score</h2>
    
    						</div>
    
    						<div aria-hidden="true"
    							class="filament-hr border-t dark:border-gray-700" style="text-align: center;">
    					
    							 
    							
    							<h1 style="font-size: 50px;">
    							<?php 
    							echo ($mailtestScore).'/10'
    							?> 
    							</h1>
     							
    							
    							</div>
    
    						<div></div>
    					</div>
    				</div>
    
    
    			</div>
    		</div>
    
    
    		<div   class="filament-widget col-span-1 filament-widgets-chart-widget" >
    			<div
    				class="p-2 space-y-2 bg-white rounded-xl shadow dark:border-gray-600 dark:bg-gray-800">
    
    
    				<div class="space-y-2">
    					<div class="px-4 py-2 space-y-4">
    						<div class="flex items-center justify-between gap-8">
    							<h2
    								class="text-xl font-semibold tracking-tight filament-card-heading">
    								Spam Assasin Score</h2>
    							<a style="color: rgb(99 102 241);" id="toggle-report" href="javascript: toggleReport();">Hide Report</a>
    						</div>
    
    						<div aria-hidden="true"
    							class="filament-hr border-t dark:border-gray-700" id="spam-report" style="display: block; text-align: center;">
    							 
    							 <canvas id="gauge" style="margin: auto;"></canvas>
    							
    							<h1 style="font-size: 50px;"><?php 
    							echo ($currentScore)
    							?> 
    							</h1>
    							<h3 style="font-size: 34px; <?php echo $color != '' ? 'color: '.$color: ''?>"><?php echo $spamScoreVal?></h3>
    							
    							
    							</div>
    						</div>
    						 
    						
    						<div></div>
    					</div>
    				</div>
    
    
    			</div>
    			
    			<div class="filament-widget col-span-2">
                    <div class="accordion" id="mailtest">
        			
                          <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                              <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse" data-bs-target="#collapseSpamIssues" aria-expanded="false" aria-controls="collapseSpamIssues">
                                Fix your Spam Issues 
                                <i class="status <?php echo $mailTestJson['spamAssassin']['statusClass']?>"></i>
                              </button>
                            </h2>
                            <div id="collapseSpamIssues" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#mailtest">
                              <div class="accordion-body">
                                <?php echo $mailTestJson['spamAssassin']['description']?>
                                <hr>
                                  <table class='table'>
                                 	<?php foreach($mailTestJson['spamAssassin']['rules'] as $rule) {
                                 	    ?>
                                 	    <tr>
                                 	    	<td class="text-center <?php echo $rule['status']?>"><?php echo $rule['score']?></td>
                                 	    	<td><?php echo $rule['code']?></td>
                                 	    	<td>
                                 	    	<?php echo $rule['description']?><br />
                                 	    	<b><?php echo $rule['solution']?></b>
                                 	    	</td>
                                 	    </tr>
                                 	    <?php 
                                 	}?>
                                 </table>
                                  <hr>
                                 <?php echo $mailTestJson['signature']['title']?> <br />
                                 <?php echo $mailTestJson['signature']['description']?> <br />
                                 <hr>
                                 
                                 <div class="accordion" id="signatureData">
                                        <?php foreach($mailTestJson['signature']['subtests'] as $titleName => $testInfo) {
                                            $accordionTitle = "subtest_".$titleName;
                                         ?>
                                          <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingOne-1">
                                              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $accordionTitle?>" aria-expanded="true" aria-controls="<?php echo $accordionTitle?>">
                                                <?php echo $testInfo['title']?>
                                                
                                                <i class="status <?php echo $testInfo['statusClass']?>"></i>
                                              </button>
                                            </h2>
                                            <div id="<?php echo $accordionTitle?>" class="accordion-collapse collapse" aria-labelledby="headingOne-1" data-bs-parent="#signatureData">
                                              <div class="accordion-body test-result">
    											<?php echo $testInfo['description']?>
    											<hr>
    											 <div class='result'>
    												<?php echo $testInfo['messages']?> 
    											</div>
                                              </div>
                                            </div>
                                          </div>
                                         
                                         <?php 
                                     }?>
                                 </div>
                              </div>
                            </div>
                          </div>
                          
                          <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                              <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse" data-bs-target="#collapseLinks" aria-expanded="false" aria-controls="collapseLinks">
                                <?php echo $mailTestJson['links']['title'];?>
                              	<i class="status <?php echo $mailTestJson['links']['statusClass']?>"></i>
                              </button>
                            </h2>
                            <div id="collapseLinks" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#mailtest">
                              <div class="accordion-body test-result">
                                <?php echo $mailTestJson['links']['description']?>
                                <hr>
                                <div class='result'>
                              		<?php echo $mailTestJson['links']['messages']?>
                              	</div>
                              </div>
                            </div>
                          </div>
                          
                          <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBlacklists" aria-expanded="false" aria-controls="collapseBlacklists">
                                 <?php echo $mailTestJson['blacklists']['title'];?>
                              	<i class="status <?php echo $mailTestJson['blacklists']['statusClass']?>"></i>
                              </button>
                            </h2>
                            <div id="collapseBlacklists" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#mailtest">
                              <div class="accordion-body">
                                <?php echo $mailTestJson['blacklists']['description']?>
                                <hr>
                                <div class='result'>
                              		<?php echo $mailTestJson['blacklists']['messages']?>
                              	</div>
                              </div>
                            </div>
                          </div>
                    </div>
                </div>
    			
    		</div>
    	</div>
	
     </div>
     <?php 
         
     }else if($newsletterScore) { 
         /*
         5+ - Do not send
         2-4.99 - Send with Caution
         0-2 - Good
         <0 - Excellent
         */
         $currentScore = $newsletterScore->spam_score;
         $spamScoreVal = '';
         
         if($currentScore >= 5) {
             $spamScoreVal = 'Do not send';
             $color = '#FF0000'; //red
         }else if($currentScore > 2 && $currentScore < 5 ) {
             $spamScoreVal = 'Send with Caution';
             $color = '#ffc800'; //orange
         }else if($currentScore > 0 && $currentScore < 2 ) {
             $spamScoreVal = 'Good';
             $color = '#0000FF'; //blue
             
         }else if($currentScore   < 0 ) {
             $spamScoreVal = 'Excellent';
             $color = '#7FFF00'; //green 
             
         } 
         $arr = explode("\n", $newsletterScore->spam_report);
         $rules = json_decode($newsletterScore->spam_rules, true);
         $index = 0 ;
         
         $newRules = [];
          
         $ruleIndex = 0;
        
         foreach($arr as $index => $rec) {
             $recInfo = explode(' ', trim($rec));
             if(is_numeric($recInfo[0])) {
                 $currentRule = $rules[$ruleIndex]; 
                 $currentRule['rule'] = trim($recInfo[1]);
                 $newRules[] = $currentRule;
                 $ruleIndex++;
             }
         } 
         
         $spamInsights = $record->getSpamInsights();
         $matchingInsights = [];
     ?>

    <div class="filament-widgets-container grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-8 mb-6" style="margin-top: 20px;">


		<div  
			class="filament-widget col-span-1 filament-widgets-chart-widget">
			<div
				class="p-2 space-y-2 bg-white rounded-xl shadow dark:border-gray-600 dark:bg-gray-800">


				<div class="space-y-2">
					<div class="px-4 py-2 space-y-4">
						<div class="flex items-center justify-between gap-8">
							<h2
								class="text-xl font-semibold tracking-tight filament-card-heading">
								Spam Score</h2>

						</div>

						<div aria-hidden="true"
							class="filament-hr border-t dark:border-gray-700" style="text-align: center;">
					
							<canvas id="gauge" style="margin: auto;"></canvas>
							
							<h1 style="font-size: 50px;"><?php 
							echo ($newsletterScore->spam_score)
							?> 
							</h1>
							<h3 style="font-size: 34px; <?php echo $color != '' ? 'color: '.$color: ''?>"><?php echo $spamScoreVal?></h3>
							
							
							</div>

						<div></div>
					</div>
				</div>


			</div>
		</div>


		<div   class="filament-widget col-span-1 filament-widgets-chart-widget" >
			<div
				class="p-2 space-y-2 bg-white rounded-xl shadow dark:border-gray-600 dark:bg-gray-800">


				<div class="space-y-2">
					<div class="px-4 py-2 space-y-4">
						<div class="flex items-center justify-between gap-8">
							<h2
								class="text-xl font-semibold tracking-tight filament-card-heading">
								Spam Report</h2>
							<a style="color: rgb(99 102 241);" id="toggle-report" href="javascript: toggleReport();">Show Report</a>
						</div>

						<div aria-hidden="true"
							class="filament-hr border-t dark:border-gray-700" id="spam-report" style="display: none;">
							 
							<table class='score-report'>
								<thead>
									<tr>
										<th>Score</th>
 										<th>Description</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($newRules as $rule) {
									    if(isset($spamInsights[$rule['rule']])) {
									        $matchingInsights[] = ['rule' => $rule['rule'], 'insights' => $spamInsights[$rule['rule']]];
									    }
									    ?>
									    <tr>
									    	<td><?php echo $rule['score']?></td>
 									    	<td>
 									    	<b><?php echo $rule['rule']?></b>
 									    	<br /><br />
 									    	<?php echo $rule['description']?></td>
									    </tr>
									    <?php 
									}?>
								</tbody>
							</table>
						</div>
						 
						
						<div></div>
					</div>
				</div>


			</div>
			
			<div
				class="p-2 space-y-2 bg-white rounded-xl shadow dark:border-gray-600 dark:bg-gray-800" style="margin-top: 20px;">


				<div class="space-y-2">
					<div class="px-4 py-2 space-y-4">
						
						<div class="flex items-center justify-between gap-8">
							<h2
								class="text-xl font-semibold tracking-tight filament-card-heading">
								AI Insight Report</h2>
							<a style="color: rgb(99 102 241);" id="toggle-insight-report" href="javascript: toggleInsightReport();">Show Insight Report</a>
						</div>

						<div aria-hidden="true"
							class="filament-hr border-t dark:border-gray-700" id="insight-report" style="display: none;">
							 
							<table class='score-report'>
								<thead>
									<tr>
										<th>Rule</th>
 										<th>Insights</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($matchingInsights as $matchingInsight) {
									    ?>
									    <tr>
  									    	<td><b> <?php echo $matchingInsight['rule']?></b></td>
 									    	<td><?php echo $matchingInsight['insights']?></td>
									    </tr>
									    <?php 
									}?>
								</tbody>
							</table>
						</div>
						
						<div></div>
					</div>
				</div>


			</div>
		</div>
	</div>
  <?php }?>
   
	
   <script type="text/javascript">
   function toggleReport() {
	    $('#spam-report').toggle();
	    $('#toggle-report').html(  $('#spam-report:visible').length == 1 ? 'Hide Report' : 'Show Report' );
	}
   function toggleInsightReport() {
	    $('#insight-report').toggle();
	    $('#toggle-insight-report').html(  $('#insight-report:visible').length == 1 ? 'Hide Insight Report' : 'Show Insight Report' );
	}
	function showSpamAnalysis() {
		$('.analysis').removeClass('btn-active');
		$('.spam-analysis').addClass('btn-active');
		$('.spam-analysis-section').show();
		$('.email-analysis-section').hide();
		$('.filament-widgets-chart-widget').show();
	}
	function showEmailAnalysis() {
		$('.analysis').removeClass('btn-active');
		$('.email-analysis').addClass('btn-active');
		$('.spam-analysis-section').hide();
		$('.email-analysis-section').show();
		$('.filament-widgets-chart-widget').hide();
	}
   $( document ).ready(function() {
	    setupGauge();
	    $('.spam-analysis').on('click', showSpamAnalysis);
	    $('.email-analysis').on('click', showEmailAnalysis);
	});
   var gauge = false;
	function setupGauge() {

		var color = '<?php echo $color?>';
		
		
   var opts = {
		   angle: -0.2, // The span of the gauge arc
		   lineWidth: 0.2, // The line thickness
		   radiusScale: 1, // Relative radius
		   pointer: {
		     length: 0.6, // // Relative to gauge radius
		     strokeWidth: 0.035, // The thickness
		     color: '#000000' // Fill color
		   },
		   limitMax: false,     // If false, max value increases automatically if value > maxValue
		   limitMin: false,     // If true, the min value of the gauge will be fixed
		   colorStart: color,   // Colors
		   colorStop: color,    // just experiment with them
		   strokeColor: '#E0E0E0',  // to see which ones work best for you
		   generateGradient: true,
		   highDpiSupport: true,     // High resolution support

		   staticZones: [
			   {strokeStyle: "#FF0000", min: 100, max: 130}, // Red from 100 to 130
			   {strokeStyle: "#ffc800", min: 2, max: 5}, // Yellow
			   {strokeStyle: "#0000FF", min: 0, max: 2}, // orange
			   {strokeStyle: "#7FFF00", min: -5, max: 0}, // Green
 			],
		 };

   		<?php   if($newsletterScore) { ?>
		 var target = document.getElementById('gauge'); // your canvas element
		 gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
		 gauge.maxValue = 10; // set max gauge value
		 gauge.minValue = -5;  // Prefer setter over gauge.minValue = 0
		 gauge.animationSpeed = 32; // set animation speed (32 is default value)
		 gauge.set(<?php echo ($newsletterScore->spam_score)?> ); // set actual value
		 <?php }?>
	}
  </script>  
</div>