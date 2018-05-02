# recrypt-count
A cli tool to count recrypt block info

This tool need a recryptd node and a mongodb
The config can be modified at config.php

## Usage:
sync all blocks to mongodb: php count.php
sync recent blocks to mongodb (used at crontab): php count.php sync (50)
cal the block time span: php count.php span 20000 30000
cal the coins' amount which mined a block: php count.php miningcoin 20000 30000
