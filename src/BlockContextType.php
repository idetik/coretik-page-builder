<?php

namespace Coretik\PageBuilder;

enum BlockContextType
{
    case PARENT;
    case SIBLING;
    case RELATED;
    case OTHER;
}
