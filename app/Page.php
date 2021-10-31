<?php

namespace App;

use Carbon\Carbon;
use App\Common\Imageable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes, Imageable;

    const VISIBILITY_PUBLIC    = 1;         //Default
    const VISIBILITY_MEMBERS_ONLY  = 2;

    const PAGE_FAQ                  = 'faqs';                       //FAQs page
    const PAGE_ABOUT_US             = 'about-us';                   //About us page
    const PAGE_CONTACT_US           = 'contact-us';                 //Contact us page
    const PAGE_PRIVACY_POLICY       = 'privacy-policy';             //The privacy policy page
    const PAGE_TNC                  = 'terms-of-use';               //Terms and condiotion page for customers
    const PAGE_RETURN_AND_REFUND    = 'return-and-refund-policy';    //Return and refund policy page

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pages';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['published_at', 'deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
                    'author_id',
                    'title',
                    'slug',
                    'content',
                    'published_at',
                    'position',
                    'visibility',
                ];

    /**
     * Get the author for the refund.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Set the published_at for the model.
     */
    public function setPublishedAtAttribute($value)
    {
        $this->attributes['published_at'] = $value ? date("Y-m-d H:i:s", strtotime($value)) : null;
    }

    /**
     * Scope a query to only include published blogs.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<', Carbon::now());
    }

    /**
     * Scope a query to only include records that have the given visibility.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisibilityOf($query, $visibility)
    {
        return $query->where('visibility', $visibility);
    }

    public function viewPosition()
    {
        switch ($this->position) {
            case 'copyright_area'    : return trans("app.copyright_area");
            case 'footer_1st_column' : return trans("app.footer_1st_column");
            case 'footer_2nd_column' : return trans("app.footer_2nd_column");
            case 'footer_3rd_column' : return trans("app.footer_3rd_column");
            case 'main_nav'          : return trans("app.main_nav");
        }
    }

    public function visibilityName()
    {
        switch ($this->visibility) {
            case static::VISIBILITY_PUBLIC: return '<span class="label label-primary">' . trans('app.public') . '</span>';
            case static::VISIBILITY_MEMBERS_ONLY: return '<span class="label label-outline">' . trans('app.members_only') . '</span>';
        }
    }
}
