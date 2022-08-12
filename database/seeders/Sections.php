<?php
namespace Database\Seeders;

use Csgt\Cancerbero\CsgtModule;

// new CsgtModule($aName, $aDescription, $aModule, $aMenuOrder, [$aIcon=null, $aParentModule=null, $aPermissions=CsgtModule::ALL, $aMenuPermission = 'index'])

class Sections
{
    public function get()
    {
        return collect([
            new CsgtModule('Inicio', 'Inicio', 'index', 100, 'fa fa-home', null, CsgtModule::INDEX),
            new CsgtModule('Registrar', 'Registrar', 'registros', 300, 'fa fa-car', null),
            new CsgtModule('Catálogos', '', 'catalogs', 4000, 'fa fa-book', null, []),
            new CsgtModule('Empresa', 'Catálogos - Empresa', 'catalogs.empresas', 100, 'fa fa-building', 'catalogs'),
            new CsgtModule('Usuarios', 'Catálogos - Usuarios', 'catalogs.users', 300, 'fa fa-users', 'catalogs'),
            new CsgtModule('Roles', 'Catálogos - Roles', 'catalogs.roles', 400, 'fa fa-key', 'catalogs'),
        ]);
    }
}
