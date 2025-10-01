<?php
use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
 
Breadcrumbs::for('backend.payments.index', function (BreadcrumbTrail $trail): void {
    $trail->push('Payments', route('backend.payments.index'));
});