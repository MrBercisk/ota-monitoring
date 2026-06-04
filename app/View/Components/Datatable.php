<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Datatable extends Component
{
    public string $tableId;
    public string $ajaxUrl;
    public array  $columns;
    public int    $pageLength;
    public array  $order;
    public bool   $searching;
    public bool   $responsive;

    public function __construct(
        string $ajaxUrl,
        array  $columns,
        string $tableId   = 'dataTable',
        int    $pageLength = 25,
        array  $order      = [[0, 'asc']],
        bool   $searching  = true,
        bool   $responsive = true,
    ) {
        $this->tableId   = $tableId;
        $this->ajaxUrl   = $ajaxUrl;
        $this->columns   = $columns;
        $this->pageLength = $pageLength;
        $this->order     = $order;
        $this->searching = $searching;
        $this->responsive = $responsive;
    }

    public function render()
    {
        return view('components.datatable');
    }
}