<?php

namespace Alvarez\ConcretePhp\Console\Commands;

use Illuminate\Console\Command;

class UI
{
    public static function displayLogo(Command $command)
    {
$logo = "
<fg=cyan>  ██████╗  ██████╗ ███╗   ██╗ ██████╗ ██████╗ ███████╗████████╗███████╗ </></>
<fg=cyan> ██╔════╝ ██╔═══██╗████╗  ██║██╔════╝ ██╔══██╗██╔════╝╚══██╔══╝██╔════╝ </></>
<fg=cyan> ██║      ██║   ██║██╔██╗ ██║██║      ██████╔╝█████╗     ██║   █████╗   </></>
<fg=cyan> ██║      ██║   ██║██║╚██╗██║██║      ██╔══██╗██╔══╝     ██║   ██╔══╝   </></>
<fg=cyan> ╚██████╗ ╚██████╔╝██║ ╚████║╚██████╗ ██║  ██║███████╗   ██║   ███████╗ </></>
<fg=cyan>  ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝ ╚═════╝ ╚═╝  ╚═╝╚══════╝   ╚═╝   ╚══════╝ </></>
";


        $command->line($logo);
        $command->newLine();
    }
}