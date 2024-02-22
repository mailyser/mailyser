<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request; 
use App\Models\User; 
use App\Models\Newsletter;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use App\Models\Sender;
use Carbon\Carbon;
use App\Enums\NewsletterStatusEnum;

class EmailTestController extends ApiController
{
  
  public function list(Request $request) {
    $this->validateApiKey($request);
    
    $newsletters = Newsletter::where("user_id", $this->currentUser->id)->orderBy("id", "desc")->get();
    
    $resp = [];
    $resp['status'] = 1;
    $resp['email_tests'] = [];
    
    foreach($newsletters as $newsletter) {
      $resp['email_tests'][] = ['id' => $newsletter->id, 
        'name' => $newsletter->name,
        
        'keyword' => $newsletter->keyword,
        'sender' => $newsletter->sender ? $newsletter->sender->email_address : '',
        'status' => $newsletter->status];
    }
    
    return response()->json($resp, 200); 
  }
  
  public function details(Request $request) {
    $this->validateApiKey($request);
    
    $resp = [];
    
    if($request->has('id') && $request->id) {
      
      Filament::auth()->loginUsingId($this->currentUser->id, true);
      
      
      $newsletter = Newsletter::where("user_id", $this->currentUser->id)->where("id", $request->id)->first();
       
      if(!$newsletter) {
        $resp['status'] = 0;
        $resp['message'] ="Invalid ID";
        
      } else {
         
        $resp['status'] = 1;
          
        $data = ['id' => $newsletter->id,
          'name' => $newsletter->name,
          'keyword' => $newsletter->keyword,
          'sender' => $newsletter->sender ? $newsletter->sender->email_address : '',
          'status' => $newsletter->status,
        ];
        
        if($newsletter->finishedScanning() && $newsletter->has_mail_tester == 1) {
          $mailTestJson = $newsletter->getMailtesterData();
          $newsletterScore = $newsletter->processSpamScore();
          
          $data['data'] = $mailTestJson;
          
          $mailtestScore = 10 + $mailTestJson['mark'];
          
          $spamScoreVal = '';
           
          if($mailtestScore <= 2.5) { 
            $spamScoreVal = 'Do not send';
          }else if($mailtestScore > 2.5 && $mailtestScore <= 5 ) {
            
            $spamScoreVal = 'Send with Caution';
          }else if($mailtestScore > 5 && $mailtestScore < 7.5 ) {
            $spamScoreVal = 'Good';
            
          }else if($mailtestScore   >= 7.5 ) { 
            $spamScoreVal = 'Excellent';
          }
          
          $data['content_score'] = $mailtestScore.'/10';
          $data['spam_score'] = spamScore($newsletterScore? $newsletterScore->spam_score : 0). '/10';
          $data['spam_score_val'] = $spamScoreVal;
          
          $dataRec = [];
          
          $newsletter->emails->each(function ($email) use (&$dataRec) {
            if ($email->pivot->status === 'scanned') {
              $landedIn = $email->pivot->found_at_mailbox;
              
              if (! isset($dataRec[$landedIn])) {
                $dataRec[$landedIn] = 0;
              }
              
              $dataRec[$landedIn]++;
            }
          });
            
          if(isset($dataRec["Not found"]))
            unset($dataRec["Not found"]);
              
          $spam_chart_data = [
                'datasets' => [
                  [
                    'data' => array_values($dataRec),
                    'backgroundColor' => array_values(array_map(function () {
                    return '#'
                      . str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT)
                      . str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT)
                      . str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
                    }, $dataRec))
                    ],
                    ],
                    'labels' => array_keys($dataRec),
                    ];
              
          $data['spam_chart_data'] = $spam_chart_data;
          
          $dataRec1 = [0, 0];
          
          $newsletter->emails->each(function ($email) use (&$dataRec1) {
            if (filled($email->pivot->status) && $email->pivot->status !== 'skipped') {
              $statusIndex = $email->pivot->found_at_mailbox === 'spam'
                ? 1 : 0;
                
                $landedIn = $email->pivot->found_at_mailbox;
                
                if($landedIn != 'Not found') {
                  $dataRec1[$statusIndex]++;
                }
            }
          });
            
          $spam_delivery_data = [
              'datasets' => [
                [
                  'data' => $dataRec1,
                  'backgroundColor' => [
                    '#10b981',
                    '#eab308',
                  ]
                ],
              ],
              'labels' => [
                'Healthy',
                'Spam',
              ],
            ];
            
            
          $data['spam_delivery_data'] = $spam_delivery_data;
          
          
        }
        
        $resp['details'] = $data;
      }
       
    } else {
      $resp['message'] = "Missing 'ID'";
      
    }
    
    
    return response()->json($resp, 200);  
  }
  
  public function create(Request $request) {
    $this->validateApiKey($request);
    
    $resp = [];
    $resp['status'] = 0;
    
    Filament::auth()->loginUsingId($this->currentUser->id, true);
    
    $sender = Sender::where("user_id", $this->currentUser->id)->where("enabled", 1)->where("id", $request->sender_id)->first();
    
    if($sender) {
      $newsletter = new Newsletter();
      $newsletter->sender_id = $request->sender_id;
      $newsletter->name = $request->internal_name;
      $newsletter->keyword = $request->keyword;
      $newsletter->user_id = $this->currentUser->id;
      $newsletter->has_mail_tester = 1;
      $newsletter->save();
      
      
      $items = $newsletter->getOrGenerateAudience();
      
      
      $emailList = [];
      if($newsletter->has_mail_tester) {
        $emailList[] = $newsletter->getMailTestUniqueEmail();
      }
      foreach($items as $item) {
        $emailList[] = $item['email'];
        
      }
      
      
      if ($newsletter->status === NewsletterStatusEnum::Draft->name) {
        $newsletter->setStatus(NewsletterStatusEnum::Waiting->name);
      }
      
      $resp['status'] = 1;
      $resp['id'] = $newsletter->id;
      $resp['emails'] = $emailList;
      $resp['status'] = $newsletter->status;
      
      
      
    } else {
      $resp['message'] = "Invalid Sender";
    }
    
    return response()->json($resp, 200);  
  }
  
  public function startScan(Request $request) {
    $this->validateApiKey($request);
    
    $resp = [];
    
    if($request->has('id') && $request->id) {
      
      Filament::auth()->loginUsingId($this->currentUser->id, true);
      
      
      $newsletter = Newsletter::where("user_id", $this->currentUser->id)->where("id", $request->id)->first();
      
      if(!$newsletter) {
        $resp['status'] = 0;
        $resp['message'] ="Invalid ID";
        
      } else {
        
        $resp['status'] = 1;
         
        $newsletter->setStatus(NewsletterStatusEnum::Sent->name);
    
        $newsletter->update([
          'scheduled_for' => Carbon::now()->addMinutes(5),
        ]);
        
        $newsletter->setStatus(NewsletterStatusEnum::Scanning->name);
         
      }
    } else {
      $resp['message'] = "Missing 'ID'";
      
    }
    
    
    return response()->json($resp, 200);  
  }
    
}
