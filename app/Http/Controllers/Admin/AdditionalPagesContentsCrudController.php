<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdditionalPagesContentsRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AdditionalPagesContentsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AdditionalPagesContentsCrudController extends CrudController
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
        CRUD::setModel(\App\Models\AdditionalPagesContents::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/additional-pages-contents');
        CRUD::setEntityNameStrings('Контент дополнительной страницы', 'Контент дополнительной страницы');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('sort')->label('сортировка');
        CRUD::column('content')->label('контент');
        // CRUD::column('additional_page_id')->label('страница');
        CRUD::addColumn([
            'name' => 'additional_page_id',
            'label' => 'страница',
            'type' => 'select',
            'entity' => 'additionalPages',
            'model' => 'App\Models\AdditionalPagesContents',
            'attribute' => 'title'
        ]);
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
        CRUD::setValidation(AdditionalPagesContentsRequest::class);

        CRUD::field('sort')->label('сортировка');
        CRUD::addField([
            'name'  => 'content',
            'label' => 'контент',
            'type'  => 'summernote',
            'options' => [],
        ]);
        $this->crud->addField([
            'name' => 'additional_page_id',
            'label' => 'страница',
            'type' => 'select',
            'model' => 'App\Models\AdditionalPages',
            'attribute' => 'title'
        ]);
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
