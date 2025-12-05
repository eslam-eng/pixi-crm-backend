<!DOCTYPE html>
<html>
<head>
    <title>{{ $subjectLine ?? 'Opportunity Items' }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f4f4f4; padding: 10px; text-align: center; }
        .content { margin-top: 20px; }
        .item-list { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .item-list th, .item-list td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        .item-list th { background-color: #f2f2f2; }
        .footer { margin-top: 20px; font-size: 0.8em; color: #777; text-align: center; }
        .thumbnail { max-width: 100px; max-height: 100px; object-fit: cover; }
        .media-gallery { display: flex; flex-wrap: wrap; gap: 5px; }
        .media-gallery img { max-width: 50px; max-height: 50px; object-fit: cover; border: 1px solid #ddd; }
        .file-link { display: block; margin-bottom: 5px; color: #007bff; text-decoration: none; }
        .file-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ $subjectLine ?? 'Opportunity Items Details' }}</h2>
        </div>
        
        <div class="content">
            <p>Dear {{ $lead->contact->name ?? 'Customer' }},</p>
            
            <p>Here are the details of the items discussed for your opportunity:</p>

            @if($items->isNotEmpty())
                <table class="item-list">
                    <thead>
                        <tr>
                            @foreach($selectedColumns as $column)
                                <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                @foreach($selectedColumns as $column)
                                    <td>
                                        @if($column === 'thumbnail_image')
                                            @if($item->getFirstMediaUrl('images'))
                                                <img src="{{ $item->getFirstMediaUrl('images') }}" alt="{{ $item->name }}" class="thumbnail">
                                            @else
                                                N/A
                                            @endif
                                        @elseif($column === 'media_images')
                                            <div class="media-gallery">
                                                @forelse($item->getMedia('images') as $media)
                                                    <a href="{{ $media->getUrl() }}" target="_blank">
                                                        <img src="{{ $media->getUrl() }}" alt="Image">
                                                    </a>
                                                @empty
                                                    N/A
                                                @endforelse
                                            </div>
                                        @elseif($column === 'files')
                                            @forelse($item->getMedia('documents') as $media)
                                                <a href="{{ $media->getUrl() }}" target="_blank" class="file-link">
                                                    {{ $media->file_name }}
                                                </a>
                                            @empty
                                                N/A
                                            @endforelse
                                        @elseif($column === 'price')
                                            {{ number_format($item->price, 2) }}
                                        @else
                                            {{ $item->$column ?? 'N/A' }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            
            <p>If you have any questions, please feel free to contact us.</p>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>