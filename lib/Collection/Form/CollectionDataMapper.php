<?php

namespace Netgen\BlockManager\Collection\Form;

use Symfony\Component\Form\DataMapperInterface;

final class CollectionDataMapper implements DataMapperInterface
{
    public function mapDataToForms($data, $forms)
    {
        $forms = iterator_to_array($forms);

        $forms['offset']->setData($data->offset);
        $forms['limit']->setData($data->limit !== 0 ? $data->limit : null);
    }

    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);
        $limit = $forms['limit']->getData();

        $data->offset = $forms['offset']->getData();
        $data->limit = $limit !== null ? $limit : 0;
    }
}