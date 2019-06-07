<?php

namespace Yajra\Auditable;

use App\User;
use Illuminate\Database\Eloquent\Builder;

trait AuditableTrait
{
    /**
     * Boot the audit trait for a model.
     *
     * @return void
     */
    public static function bootAuditableTrait()
    {
        static::observe(new AuditableTraitObserver);
    }

    /**
     * Get user model who created the record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo($this->getUserInstance(), $this->getCreatedByColumn());
    }

    /**
     * Get column name for created by.
     *
     * @return string
     */
    protected function getCreatedByColumn()
    {
        return defined('static::CREATED_BY') ? static::CREATED_BY : 'created_by';
    }

    /**
     * Get user model who updated the record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater()
    {
        return $this->belongsTo($this->getUserInstance(), $this->getUpdatedByColumn());
    }

    /**
     * Get column name for updated by.
     *
     * @return string
     */
    protected function getUpdatedByColumn()
    {
        return defined('static::UPDATED_BY') ? static::UPDATED_BY : 'updated_by';
    }

    /**
     * Get user model who deleted the record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deleter()
    {
        return $this->belongsTo($this->getUserInstance(), $this->getDeletedByColumn());
    }

    /**
     * Get column name for deleted by.
     *
     * @return string
     */
    protected function getDeletedByColumn()
    {
        return defined('static::DELETED_BY') ? static::DELETED_BY : 'deleted_by';
    }

    /**
     * Get created by user full name.
     *
     * @return string
     */
    public function getCreatedByNameAttribute()
    {
        if ($this->{$this->getCreatedByColumn()}) {
            return $this->creator->first_name . ' ' . $this->creator->last_name;
        }

        return '';
    }

    /**
     * Get Laravel's user class instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getUserInstance()
    {
        $class = config('auth.providers.users.model', User::class);

        return new $class;
    }

    /**
     * Get updated by user full name.
     *
     * @return string
     */
    public function getUpdatedByNameAttribute()
    {
        if ($this->{$this->getUpdatedByColumn()}) {
            return $this->updater->first_name . ' ' . $this->updater->last_name;
        }

        return '';
    }

    /**
     * Get deleted by user full name.
     *
     * @return string
     */
    public function getDeletedByNameAttribute()
    {
        if ($this->{$this->getDeletedByColumn()}) {
            return $this->deleter->first_name . ' ' . $this->deleter->last_name;
        }

        return '';
    }

    /**
     * Query scope to limit results to own records.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwned(Builder $query)
    {
        return $query->where($this->getQualifiedUserIdColumn(), auth()->id());
    }

    /**
     * Get qualified column name for user id.
     *
     * @return string
     */
    public function getQualifiedUserIdColumn()
    {
        return $this->getTable() . '.' . $this->getUserInstance()->getKey();
    }
}
