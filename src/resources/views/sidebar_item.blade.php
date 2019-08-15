@foreach($dirs as $dir)
    <li class="nav-item">
      <a class="nav-link" href="#" data-type="0">
        <i class="fa fa-folder fa-fw"></i> {{ $dir->name }}
      </a>
      <ul class="nav nav-pills flex-column" node={{ $dir->id }} >
      </ul>
    </li>
@endforeach
