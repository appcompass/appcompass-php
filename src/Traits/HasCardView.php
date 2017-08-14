<?php

namespace P3in\Traits;

trait HasCardView
{
    public function __construct(array $attributes = [])
    {
        $this->append('card_photo');

        parent::__construct($attributes);
    }

    abstract public function getCardPhotoUrl();

    public function getCardPhotoAttribute()
    {
        return $this->getCardPhotoUrl();
    }

    public function setCardPhotoAttribute($val)
    {
        // do nothing.  @TODO: We do need to fix this, as we shouldn't have to ignore relationship methods in the post.  i.e. the repo should handle filtering this out.
    }
}
