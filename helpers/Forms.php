<?php


class Form
{

    public static function input($type = "text", $id, $name, $value = "", $label, $placeholder = "")
    {
        return "
            <div class='flex flex-col w-full gap-2'>
                <label for='{$id}' class='block text-sm font-medium'>{$label}</label>
                <input
                    type='{$type}'
                    id='{$id}'
                    name='{$name}'
                    class='input-component'
                    required
                    value='{$value}' 
                    placeholder='{$placeholder}'
                />
            </div>
        ";
    }

    public static function textarea($id, $name, $value = "", $label, $placeholder = "")
    {
        return "
            <div class='flex flex-col w-full gap-2'>
                <label for='{$id}' class='block text-sm font-medium'>{$label}</label>
                <textarea
                    id='{$id}'
                    name='{$name}'
                    class='textarea-component'
                    placeholder='{$placeholder}'
                    required
                >{$value}</textarea>
            </div>
        ";
    }
}
