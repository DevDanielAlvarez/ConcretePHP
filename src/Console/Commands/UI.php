<?php

namespace Alvarez\ConcretePhp\Console\Commands;

use Illuminate\Console\Command;

class UI
{
    public static function displayLogo(Command $command)
    {
        $logo = "
 <fg=cyan>____ ___  _   _  ____ ____  _____ _____ _____   ____  _   _ ____  </>
<fg=cyan>/ ___/ _ \| \ | |/ ___|  _ \| ____|_   _| ____| |  _ \| | | |  _ \ </>
<fg=cyan>| |  | | | |  \| | |   | |_) |  _|   | | |  _|   | |_) | |_| | |_) |</>
<fg=cyan>| |__| |_| | |\  | |___|  _ <| |___  | | | |___  |  __/|  _  |  __/ </>
<fg=cyan>\____\___/|_| \_|\____|_| \_\_____| |_| |_____| |_|   |_| |_|_|    </>
        ";

        $command->line($logo);
        $command->newLine();
    }
}