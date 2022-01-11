<?php

function ticket($ticket)
{
    return('#'.str_pad($ticket,10,'0',STR_PAD_LEFT));
}