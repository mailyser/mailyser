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
     if($newsletterScore) { 
         /*
         5+ - Do not send
         2-4.99 - Send with Caution
         0-2 - Good
         <0 - Excellent
         */
         $currentScore = $newsletterScore->spam_score;
         $spamScoreVal = '';
         $color = '';
         if($currentScore >= 5) {
             $spamScoreVal = 'Do not send';
             $color = 'red';
         }else if($currentScore > 2 && $currentScore < 5 ) {
             $spamScoreVal = 'Send with Caution';
             $color = 'yellow';
         }else if($currentScore > 0 && $currentScore < 2 ) {
             $spamScoreVal = 'Good';
             $color = 'blue';
             
         }else if($currentScore   < 0 ) {
             $spamScoreVal = 'Excellent';
             $color = 'green';
             
         }
         
     ?>

    <div class="filament-widgets-container grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-8 mb-6">


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
							class="filament-hr border-t dark:border-gray-700">
							
							<h1 style="font-size: 50px;"><?php 
							echo ($newsletterScore->spam_score)
							?> 
							</h1>
							<h3 style="<?php echo $color != '' ? 'color: '.$color: ''?>"><?php echo $spamScoreVal?></h3>
							</div>

						<div></div>
					</div>
				</div>


			</div>
		</div>


	<div   class="filament-widget col-span-1 filament-widgets-chart-widget">
			<div
				class="p-2 space-y-2 bg-white rounded-xl shadow dark:border-gray-600 dark:bg-gray-800">


				<div class="space-y-2">
					<div class="px-4 py-2 space-y-4">
						<div class="flex items-center justify-between gap-8">
							<h2
								class="text-xl font-semibold tracking-tight filament-card-heading">
								Spam Report</h2>
							<a style="color: rgb(99 102 241);" class='toggle-report' href="javascript: toggleReport()">Show Report</a>
						</div>

						<div aria-hidden="true"
							class="filament-hr border-t dark:border-gray-700 report-section" style="display: none;">
							
							<?php 
							echo nl2br($newsletterScore->spam_report)
							?>
						</div>

						<div></div>
					</div>
				</div>


			</div>
		</div>
	</div>
  <?php }?>
  
<!--   <script src="//code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script> -->
  <script type="text/javascript">
function toggleReport() {
	$('.report-section').toggle();
	$('.toggle-report').html( $('.report-section:visible').length == 1 ? "Hide Report" : "Show Report" );
}
  </script>