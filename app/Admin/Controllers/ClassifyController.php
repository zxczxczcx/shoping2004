<?php

namespace App\Admin\Controllers;

use App\Model\ClassifyModel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;


use Encore\Admin\Tree;
use Encore\Admin\Layout\Content;

class ClassifyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ClassifyModel';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    public function index(Content $content){
        $tree = new Tree(new ClassifyModel);

        return $content->header('树状模型')->body($tree);

        
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(ClassifyModel::findOrFail($id));

        $show->field('cat_id', __('Cat id'));
        $show->field('cat_name', __('Cat name'));
        $show->field('keywords', __('Keywords'));
        $show->field('cat_desc', __('Cat desc'));
        $show->field('parent_id', __('Parent id'));
        $show->field('sort_order', __('Sort order'));
        $show->field('template_file', __('Template file'));
        $show->field('measure_unit', __('Measure unit'));
        $show->field('show_in_nav', __('Show in nav'));
        $show->field('style', __('Style'));
        $show->field('is_show', __('Is show'));
        $show->field('grade', __('Grade'));
        $show->field('filter_attr', __('Filter attr'));
        $show->field('float_percent', __('Float percent'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ClassifyModel());

        $form->text('cat_name', __('Cat name'));
        $form->text('keywords', __('Keywords'));
        $form->text('cat_desc', __('Cat desc'));
        $form->text('parent_id', __('Parent id'))->selectoptio(ClassifyModel::);
        $form->switch('sort_order', __('Sort order'))->default(50);
        $form->text('template_file', __('Template file'));
        $form->text('measure_unit', __('Measure unit'));
        $form->switch('show_in_nav', __('Show in nav'));
        $form->text('style', __('Style'));
        $form->switch('is_show', __('Is show'))->default(1);
        $form->switch('grade', __('Grade'));
        $form->text('filter_attr', __('Filter attr'));
        $form->switch('float_percent', __('Float percent'));

        return $form;
    }
}
