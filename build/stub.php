#!/usr/bin/env php
<?php

Phar::mapPhar('dumbdump.phar');

require 'phar://dumbdump.phar/dumbdump';