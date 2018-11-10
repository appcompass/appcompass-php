<?php

namespace AppCompass\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Notification;
use AppCompass\Notifications\FormStored;

class FormStorage extends Model
{
    use Notifiable;

    protected $fillable = [
        'form_id',
        'content',
    ];

    public $table = 'form_storage';

    protected $casts = [
        'content' => 'object',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public static function store(array $content, Form $form)
    {
        $storage = new static(['content' => $content]);

        $storage->form()->associate($form);

        if ($storage->save()) {
            Notification::send(Role::whereName('admin')->first()->users, new FormStored($storage));
        }

        return $storage;
    }
}
