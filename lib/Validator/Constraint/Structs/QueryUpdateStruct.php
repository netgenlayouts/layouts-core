<?php

namespace Netgen\BlockManager\Validator\Constraint\Structs;

use Symfony\Component\Validator\Constraint;

final class QueryUpdateStruct extends Constraint
{
    public function validatedBy()
    {
        return 'ngbm_query_update_struct';
    }
}
