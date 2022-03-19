<?php 
require "twitteroauth-main/autoload.php";
require "vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;


$cONSUMER_KEY = "";
$cONSUMER_SECRET = "";
$access_token = "";
$access_token_secret = "";

$connection = new TwitterOAuth($cONSUMER_KEY, $cONSUMER_SECRET, $access_token, $access_token_secret);
//$content = $connection->get("account/verify_credentials");

//var_dump($content);

//get tweets

//$statuses = $connection->get("statuses/home_timeline", ["count" => 2, "exclude_replies" => true]);
//GET https://api.twitter.com/1.1/statuses/mentions_timeline.json?count=2&since_id=14927799



//$statues = $connection->post("statuses/update", ["status" => "This tweet was created via the twitter API!"]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/d3/3.4.11/d3.min.js"></script>
    <script src="cloud.js"></script>
    <title>twitter</title>
</head>
<body>

<form action="" method="post">
<label>search : <input type="text" name="keyword"/> </label>
<label><input type="submit" name="submit"/> </label>    
</form>
<?php 

if(isset($_POST['submit'])){

   //$statuses = $connection->get("search/tweets", ["count" => 500, "exclude_replies" => true, "q" => $_POST['keyword'], "granularity" =>"country", "result_type" => 'recent', 'search_metadata' => false ]);

   $tweets = $connection->get("search/tweets", ["count" => 100,  "q" => $_POST['keyword'],  "result_type" => 'recent',]);
   //$statuses = $connection->get("https://api.twitter.com/1.1/search/tweets.json?q=".$_POST['keyword']."&result_type=recent&count=2");
  

   $count = 0;
   
    foreach ($tweets as $tweet) {
       
        if( $count == 100){
            break;
        }

        foreach ($tweet as $twee) {
            
            // if( $count == 1){
            //     continue;
            // }
            $twe =  $twee->text.'<br>';
            $string = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $twe);
            $string1 = str_replace(' ', '-', $string);
            $string2 = preg_replace('/[^A-Za-z0-9\-]/', '', $string1);
            $string3 = str_replace('-', ' ', $string2);
            $string4 = preg_replace('/[0-9]+/', '', $string3);
            $tw[] =$string4;

            //pass the tweets to an array
            //explode the arr to a variable 
            //pass it to javascript
            //us d3 for word cloud
           // echo "<br>";
            
            

            if( $count == 100){
                break;
            }
            $count++;
            

        }
        

    }

    $tweet_var = implode(" ", $tw);
    //echo $tweet_var;
//exit();


 }
 
//------------------------------------------
?>




<div id="chart"></div>
    <script>
        
        var text_string = '<?=$tweet_var?>';
        


        drawWordCloud(text_string);

        function drawWordCloud(text_string) {
            var common = "poop,i,me,my,myself,we,us,our,ours,ourselves,you,your,yours,yourself,yourselves,he,him,his,himself,she,her,hers,herself,it,its,itself,they,them,their,theirs,themselves,what,which,who,whom,whose,this,that,these,those,am,is,are,was,were,be,been,being,have,has,had,having,do,does,did,doing,will,would,should,can,could,ought,i'm,you're,he's,she's,it's,we're,they're,i've,you've,we've,they've,i'd,you'd,he'd,she'd,we'd,they'd,i'll,you'll,he'll,she'll,we'll,they'll,isn't,aren't,wasn't,weren't,hasn't,haven't,hadn't,doesn't,don't,didn't,won't,wouldn't,shan't,shouldn't,can't,cannot,couldn't,mustn't,let's,that's,who's,what's,here's,there's,when's,where's,why's,how's,a,an,the,and,but,if,or,because,as,until,while,of,at,by,for,with,about,against,between,into,through,during,before,after,above,below,to,from,up,upon,down,in,out,on,off,over,under,again,further,then,once,here,there,when,where,why,how,all,any,both,each,few,more,most,other,some,such,no,nor,not,only,own,same,so,than,too,very,say,says,said,shall,amp,br";

            var word_count = {};

            var words = text_string.split(/[ '\-\(\)\*":;\[\]|{},.!?]+/);
            if (words.length == 1) {
                word_count[words[0]] = 1;
            } else {
                words.forEach(function(word) {
                    var word = word.toLowerCase();
                    if (word != "" && common.indexOf(word) == -1 && word.length > 1) {
                        if (word_count[word]) {
                            word_count[word]++;
                        } else {
                            word_count[word] = 1;
                        }
                    }
                })
            }

            var svg_location = "#chart";
            var width = $(document).width();
            var height = $(document).height();

            var fill = d3.scale.category20();

            var word_entries = d3.entries(word_count);

            var xScale = d3.scale.linear()
                .domain([0, d3.max(word_entries, function(d) {
                    return d.value;
                })])
                .range([10, 100]);

            d3.layout.cloud().size([width, height])
                .timeInterval(20)
                .words(word_entries)
                .fontSize(function(d) {
                    return xScale(+d.value);
                })
                .text(function(d) {
                    return d.key;
                })
                .rotate(function() {
                    return ~~(Math.random() * 2) * 90;
                })
                .font("Impact")
                .on("end", draw)
                .start();

            function draw(words) {
                d3.select(svg_location).append("svg")
                    .attr("width", width)
                    .attr("height", height)
                    .append("g")
                    .attr("transform", "translate(" + [width >> 1, height >> 1] + ")")
                    .selectAll("text")
                    .data(words)
                    .enter().append("text")
                    .style("font-size", function(d) {
                        return xScale(d.value) + "px";
                    })
                    .style("font-family", "Impact")
                    .style("fill", function(d, i) {
                        return fill(i);
                    })
                    .attr("text-anchor", "middle")
                    .attr("transform", function(d) {
                        return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
                    })
                    .text(function(d) {
                        return d.key;
                    });
            }

            d3.layout.cloud().stop();
        }
    </script>
    
</body>
</html>