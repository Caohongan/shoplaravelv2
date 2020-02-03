<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ProductsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Product list')
            ->body($this->grid());
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit product')
            ->body($this->form()->edit($id));
         
    }
   
    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create product')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->id('ID')->sortable();
        $grid->title('Product name');
        $grid->on_sale('It has been added to')->display(function ($value) {
            return $value ? 'Yes' : 'No';
        });
        $grid->price('Price');
        $grid->rating('Score');
        $grid->sold_count('Sales');
        $grid->review_count('Reviews');

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                
            });
        });
      

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product);

        // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
        $form->text('title', 'Product name')->rules('required');

        // 创建一个选择图片的框
        $form->image('image', 'Cover image')->rules('required|image');

        // 创建一个富文本编辑器
        $form->editor('description', 'Product description')->rules('required');

        // 创建一组单选框
        $form->radio('on_sale', 'Shelves')->options(['1' => 'Yes', '0'=> 'No'])->default('1');
        $form->tools(function (Form\Tools $tools) {

     
        
        
            $tools->disableView();
        
            
        });
        // 直接添加一对多的关联模型
        $form->hasMany('skus', 'SKU List', function (Form\NestedForm $form) {
            $form->text('title', 'SKU Name')->rules('required');
            $form->text('description', 'SKU Description')->rules('required');
            $form->text('price', 'Unit price')->rules('required|numeric|min:0.01');
            $form->text('stock', 'Remaining inventory')->rules('required|integer|min:0');
        });

        // 定义事件回调，当模型即将保存时会触发这个回调
        $form->saving(function (Form $form) {
            $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
        });

        return $form;
    }
}
