
<?php

it('can be created with factory', function () {
    $embedding = \App\Models\Embedding::factory()->create();
    expect($embedding->id)->toBeInt();
    expect($embedding->file_path)->toBeString();
    expect($embedding->embedding)->toBeString();
    expect($embedding->created_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    expect($embedding->updated_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);

});
