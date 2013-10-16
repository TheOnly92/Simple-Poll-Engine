<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
<style>
.well-poll-<?php echo $poll->Poll->Id; ?> {
    background-color: #F5F5F5;
    border: 1px solid #E3E3E3;
    border-radius: 4px 4px 4px 4px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05) inset;
    margin-bottom: 20px;
    min-height: 20px;
    padding: 19px;
    color: #333333;
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    font-size: 14px;
    line-height: 20px;
}
.well-poll-<?php echo $poll->Poll->Id; ?> h4 {
    font-size: 17.5px;
    color: inherit;
    font-family: inherit;
    font-weight: bold;
    line-height: 20px;
    margin: 10px 0;
    text-rendering: optimizelegibility;
}
.well-poll-<?php echo $poll->Poll->Id; ?> .radio {
    min-height: 20px;
    padding-left: 20px;
}
.well-poll-<?php echo $poll->Poll->Id; ?> label {
    display: block;
    margin-bottom: 5px;
}
.well-poll-<?php echo $poll->Poll->Id; ?> label, .well-poll-<?php echo $poll->Poll->Id; ?> input, .well-poll-<?php echo $poll->Poll->Id; ?> button {
    font-size: 14px;
    font-weight: normal;
    line-height: 20px;
    cursor: pointer;
    color: #333333;
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
}
.well-poll-<?php echo $poll->Poll->Id; ?> .radio input[type="radio"] {
    float: left;
    margin-left: -20px;
    width: auto;
    line-height: normal;
    cursor: pointer;
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    font-size: 14px;
    font-weight: normal;
    vertical-align: middle;
}
.well-poll-<?php echo $poll->Poll->Id; ?> button {
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    font-weight: normal;
    cursor: pointer;
}
.well-poll-<?php echo $poll->Poll->Id; ?> .btn {
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    background-color: #F5F5F5;
    background-image: linear-gradient(to bottom, #FFFFFF, #E6E6E6);
    background-repeat: repeat-x;
    border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) #B3B3B3;
    border-image: none;
    border-radius: 4px 4px 4px 4px;
    border-style: solid;
    border-width: 1px;
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05);
    color: #333333;
    cursor: pointer;
    display: inline-block;
    font-size: 14px;
    line-height: 20px;
    margin-bottom: 0;
    padding: 4px 12px;
    text-align: center;
    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
    vertical-align: middle;
}
.well-poll-<?php echo $poll->Poll->Id; ?> .btn-primary {
    background-color: #006DCC;
    background-image: linear-gradient(to bottom, #0088CC, #0044CC);
    background-repeat: repeat-x;
    border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
    color: #FFFFFF;
    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
}
.well-poll-<?php echo $poll->Poll->Id; ?> .progress {
    background-color: #F7F7F7;
    background-image: linear-gradient(to bottom, #F5F5F5, #F9F9F9);
    background-repeat: repeat-x;
    border-radius: 4px 4px 4px 4px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) inset;
    height: 20px;
    margin-bottom: 20px;
    overflow: hidden;
}
.well-poll-<?php echo $poll->Poll->Id; ?> .progress .bar {
    -moz-box-sizing: border-box;
    background-color: #0E90D2;
    background-image: linear-gradient(to bottom, #149BDF, #0480BE);
    background-repeat: repeat-x;
    box-shadow: 0 -1px 0 rgba(0, 0, 0, 0.15) inset;
    color: #FFFFFF;
    float: left;
    font-size: 12px;
    height: 100%;
    text-align: center;
    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
    transition: width 0.6s ease 0s;
    width: 0;
}
</style>