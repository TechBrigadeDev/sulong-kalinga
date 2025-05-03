<?php

namespace Database\Factories;

use App\Models\MessageAttachment;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MessageAttachment>
 */
class MessageAttachmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MessageAttachment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isImage = $this->faker->boolean(70); // 70% chance of being an image
        
        if ($isImage) {
            $fileName = $this->faker->word . '.jpg';
            $filePath = 'message_attachments/images/' . $fileName;
            $fileType = 'image/jpeg';
        } else {
            $fileExtension = $this->faker->randomElement(['pdf', 'doc', 'docx']);
            $fileName = $this->faker->word . '.' . $fileExtension;
            $filePath = 'message_attachments/documents/' . $fileName;
            
            if ($fileExtension === 'pdf') {
                $fileType = 'application/pdf';
            } elseif ($fileExtension === 'doc') {
                $fileType = 'application/msword';
            } else {
                $fileType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            }
        }
        
        return [
            'message_id' => Message::factory(),
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $this->faker->numberBetween(10000, 5000000), // 10KB to 5MB
            'is_image' => $isImage,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    /**
     * Configure the attachment as an image.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function asImage(): Factory
    {
        return $this->state(function (array $attributes) {
            $fileName = $this->faker->word . '.jpg';
            return [
                'file_name' => $fileName,
                'file_path' => 'message_attachments/images/' . $fileName,
                'file_type' => 'image/jpeg',
                'is_image' => true,
            ];
        });
    }
    
    /**
     * Configure the attachment as a document.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function asDocument(): Factory
    {
        return $this->state(function (array $attributes) {
            $fileExtension = $this->faker->randomElement(['pdf', 'doc', 'docx']);
            $fileName = $this->faker->word . '.' . $fileExtension;
            
            if ($fileExtension === 'pdf') {
                $fileType = 'application/pdf';
            } elseif ($fileExtension === 'doc') {
                $fileType = 'application/msword';
            } else {
                $fileType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            }
            
            return [
                'file_name' => $fileName,
                'file_path' => 'message_attachments/documents/' . $fileName,
                'file_type' => $fileType,
                'is_image' => false,
            ];
        });
    }
}