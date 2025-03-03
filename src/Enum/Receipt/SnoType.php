<?php

namespace PayKassa\Enum\Receipt;

enum SnoType: string
{
    case OSN = 'osn';
    case USN_INCOME = 'usn_income';
    case USN_INCOME_OUTCOME = 'usn_income_outcome';
    case ENVD = 'envd';
    case ESN = 'esn';
    case PATENT = 'patent';
}
