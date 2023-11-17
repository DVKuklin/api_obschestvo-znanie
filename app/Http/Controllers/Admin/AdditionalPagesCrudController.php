<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdditionalPagesRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AdditionalPagesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AdditionalPagesCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\AdditionalPages::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/additional-pages');
        CRUD::setEntityNameStrings('дополнительная страница', 'дополнительные страницы');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('title')->label('заголовок');
        CRUD::column('sort')->label('сортировка');
        // CRUD::column('icon');
        $this->crud->addColumn([
            'name'=>'icon',
            'label'=>'картинка',
            'type'=>'image',
            'prefix'=>'storage/'
        ]);
        CRUD::column('description')->label('описание');
        CRUD::column('url')->label('url');
        CRUD::addColumn([
            'name' => 'is_published',
            'label' => 'Опубликовано',
            'type' => 'check',
        ]);
        // CRUD::column('created_at');
        // CRUD::column('updated_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }
    protected function setupShowOperation() {
        $this->setupListOperation();
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(AdditionalPagesRequest::class);

        CRUD::field('title')->label('заголовок');
        CRUD::field('sort')->label('сортировка');
        // CRUD::field('icon');
        $this->crud->addField([
            'name'=>'icon',
            'label'=>'иконка',
            'type'=>'upload',
            'upload'=> true,
            'disk'=>'public' 
        ]);
        CRUD::field('description')->label('описание')->type('textarea');
        CRUD::field('url');
        $this->crud->addField([
            'name' => 'is_published',
            'label' => 'Опубликовать',
            'type' => 'switch',
        ]);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
