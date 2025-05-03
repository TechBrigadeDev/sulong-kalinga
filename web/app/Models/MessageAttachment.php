<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    use HasFactory;
    
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'attachment_id';
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'message_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'is_image'
    ];
    
    /**
     * Get the message that this attachment belongs to.
     */
    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }
    
    /**
     * Get the full URL for the attachment.
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
    
    /**
     * Determine if the attachment is an image.
     */
    public function isImage()
    {
        return $this->is_image;
    }
    
    /**
     * Determine if the attachment is a document.
     */
    public function isDocument()
    {
        return !$this->is_image;
    }
    
    /**
     * Get the human-readable file size.
     */
    public function getHumanReadableSizeAttribute()
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}