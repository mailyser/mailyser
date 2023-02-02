<div>
<script src="//code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/gauge.js/1.2.1/gauge.min.js" integrity="sha512-CvDF0JVxliK2VV8gGA7qEEyRPcORRA2miPvpDhXvlfw0TpbGAmoQHMmEP2eziwKLsNz8PaoNfs4yjnlcpn4E3w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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
     if($newsletterScore) { 
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
         var_dump($spamInsights);
         die;
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
		</div>
	</div>
  <?php }?>
   <script type="text/javascript">
   function toggleReport() {
	    $('#spam-report').toggle();
	    $('#toggle-report').html(  $('#spam-report:visible').length == 1 ? 'Hide Report' : 'Show Report' );
	}
   $( document ).ready(function() {
	    setupGauge();
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
 
		 var target = document.getElementById('gauge'); // your canvas element
		 gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
		 gauge.maxValue = 10; // set max gauge value
		 gauge.minValue = -5;  // Prefer setter over gauge.minValue = 0
		 gauge.animationSpeed = 32; // set animation speed (32 is default value)
		 gauge.set(<?php echo ($newsletterScore->spam_score)?> ); // set actual value
	}
  </script>
  <style>
    table.score-report {
	   width: 100%;
    }
    table.score-report, table.score-report th, table.score-report td {
      border: 1px solid;
      font-size: 14px;
      padding: 5px;
    }
  </style>
</div>