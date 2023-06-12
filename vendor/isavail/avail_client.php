<?php

#  Copyright (C) 2017 Registro.br. All rights reserved.
# 
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions are
# met:
# 1. Redistribution of source code must retain the above copyright
#    notice, this list of conditions and the following disclaimer.
# 2. Redistributions in binary form must reproduce the above copyright
#    notice, this list of conditions and the following disclaimer in the
#    documentation and/or other materials provided with the distribution.
# 
# THIS SOFTWARE IS PROVIDED BY REGISTRO.BR ``AS IS AND ANY EXPRESS OR
# IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
# WARRANTIE OF FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO
# EVENT SHALL REGISTRO.BR BE LIABLE FOR ANY DIRECT, INDIRECT,
# INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
# BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS
# OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
# ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR
# TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
# USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
# DAMAGE.

# $Id$

require "Avail.php";

#############################################################
##                                                         ##
##                Command-line client                      ##
##                                                         ##
#############################################################
function usage() {
    print "\n";
    print "Usage:\n";
    print "\tphp avail_client.php [-h] [-d] [-l language] [-s server_IP]\n";
    print "\t                     [-p server_port] [-c cookie_file] \n";
    print "\t                     [-a proxied_IP] [-S] fqdn\n\n";
    print "\t-h Print this help\n";
    print "\t-d Turn ON debug mode\n";
    print "\t-l language: EN or PT (Default: PT)\n";
    print "\t-s server_IP: Server's IP address (Default: " . SERVER_ADDR . ")\n";
    print "\t-p server_port: Server's port number (Default: " . SERVER_PORT . ")\n";
    print "\t-c cookie_file: File where the cookie is stored\n";
    print "\t   (Default: " . COOKIE_FILE . ")\n";
    print "\t-a proxied_IP: Client IP address being proxied\n";
    print "\t-S Enable suggestion in server answer\n";
    print "\t-6 Force IPv6\n";
    print "\tfqdn: fully qualified domain name being queried\n";
    print "\n";
}

function getopts($argv, $argc, &$last_param) {
   $i = 1;
   $params = array();
   for ($i = 1; $i < $argc; $i++){
       if (isParam($argv[$i])){
           if (strlen($argv[$i]) == 2){
               # seperated (ie -l PT)  or no value parameter (ie. -h)
               $paramName = $argv[$i];
               $paramVal = ( !isParam($argv[$i + 1]) ) ? $argv[$i + 1] : true;

           } else if (strlen($argv[$i]) > 2){
               # joined parameters ie -lPT
               $paramName = substr($argv[$i], 0, 2);
               $paramVal = substr($argv[$i], 2);
           }

           $params[$paramName] = $paramVal;
       } else {
           $last_param = $argv[$i];
       }
   }

   return $params;
}

# determines whether string is a parameter
function isParam($string){
    return ($string[0] == "-") ? 1: 0;
}

# Get the command line options
$opt = getopts($argv, $argc, $fqdn);
if ($argc < 2)
{
  usage();
  exit(1);
}

if (@$opt["-h"] == true) {
  usage();
  exit(1);
}

# Default parameters
$debug = (@$opt["-d"] == true) ? true : false;

$atrib = array();
$atrib["lang"]        = (strtoupper(@$opt["-l"]) == "EN") ? 0 : 1;
$atrib["server"]      = (@$opt["-s"]) ? @$opt["-s"] : SERVER_ADDR;
$atrib["port"]        = (@$opt["-p"]) ? @$opt["-p"] : SERVER_PORT;
$atrib["cookie_file"] = COOKIE_FILE;
$atrib["ip"]          = (@$opt["-a"]) ? @$opt["-a"] : '';
$atrib["suggest"]     = (@$opt["-S"]) ? 1 : 0;
$atrib["v6"]          = (@$opt["-6"]) ? true : false;

# There must be at least one argument (FQDN)
if (strlen($fqdn) <= 1) {
    usage();
    exit(1);
}

# Initialize client object and send query
$ac = new AvailClient();
$ac->setParam($atrib);
$arp = $ac->send_query($fqdn);
print $arp->str() . "\n";

if ($debug == true) {
    print "*****Response received*****\n";
    print $arp->response . "\n";
}

?> 
