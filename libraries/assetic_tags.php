<?php

class Assetic_Tags extends TagManager_Form
{
    private static $AssetManager;
    private static $AssetReference;
    private static $AssetCollection;
    private static $_AssetCollection;
    private static $AssetWriter;
    private static $Cache;
    private static $CachePath;

    private static $Method = NULL;
    private static $Extentions = array();
    private static $Collection;
    private static $Collections = array();
    private static $Compressor = array();
    private static $Result;
    private static $RenderResult; // Maybe we can use it, keep it for the moment
    private static $OutputPath;
    private static $OutputURL;
    private static $OutputFileURL;
    private static $OutputFileName;
    private static $OutputFileTime;
    private static $Filter=NULL;

    private static $ThemePath;
    private static $ThemeURL;
    private static $GoogleJar;
    private static $GoogleJarPath;
    private static $YuiJar;
    private static $YuiJarPath;
    private static $ModuleLibraryPath;
    private static $SourcePath;

    // Directory Separator
    protected $DP;


    // ------------------------------------------------------------------------

    /**
     * Tags declaration
     * To be available, each tag must be declared in this static array.
     *
     * @var array
     *
     * @usage	"<tag scope>" => "<method_in_this_class>"
     */
    public static $tag_definitions = array
    (
        'assetic:AssetManager'                              => 'tag_AssetManager',
        'assetic:AssetManager:AssetCollection'              => 'tag_AssetCollection',
        'assetic:AssetManager:AssetCollection:FileAsset'    => 'tag_FileAsset',
        'assetic:AssetManager:AssetCollection:GlobAsset'    => 'tag_GlobAsset',
        'assetic:AssetManager:AssetCollection:Compressor'   => 'tag_Compressor',
        'assetic:AssetManager:AssetCollection:Render'       => 'tag_Render',
        'assetic:AssetManager:AssetCollection:AssetURL'     => 'tag_AssetURL'
    );

    // ------------------------------------------------------------------------

    /**
     * Base module tag
     * The index function of this class refers to the <ion:#module_name /> tag
     * In other words, this function makes the <ion:#module_name /> tag
     * available as main module parent tag for all other tags defined
     * in this class.
     *
     * @usage	<ion:assetic>
     *			...
     *			</ion:assetic>
     *
     */
    public static function index(FTL_Binding $tag)
    {
        self::load_model('assetic_model', '');

        unset(self::$ci->AssetManager);
        unset(self::$ci->AssetReference);
        unset(self::$ci->_AssetCollection);
        unset(self::$ci->AssetCollection);
        unset(self::$ci->AssetWriter);
        unset(self::$ci->Cache);
        unset(self::$ci->CachePath);
        unset(self::$ci->Method);
        unset(self::$ci->Extentions);
        unset(self::$ci->Collection);
        unset(self::$ci->Collections);
        unset(self::$ci->Compressor);
        unset(self::$ci->Result);
        unset(self::$ci->OutputPath);
        unset(self::$ci->OutputURL);
        unset(self::$ci->OutputFileURL);
        unset(self::$ci->OutputFileName);
        unset(self::$ci->OutputFileTime);

        self::$ci->DP = '/';

        self::$ci->GoogleJar    = 'compiler.jar';
        self::$ci->YuiJar       = 'yuicompressor.jar';
        self::$ci->CachePath            = config_item('cache_path');
        self::$ci->ThemePath            = FCPATH . Theme::get_theme_path();
        self::$ci->ThemeURL             = base_url() . Theme::get_theme_path();
        self::$ci->ModuleLibraryPath    = MODPATH . 'Assetic' . self::$ci->DP . 'libraries' . self::$ci->DP;
        self::$ci->GoogleJarPath        = self::$ci->ModuleLibraryPath . 'Compressor' . self::$ci->DP . self::$ci->GoogleJar;
        self::$ci->YuiJarPath           = self::$ci->ModuleLibraryPath . 'Compressor' . self::$ci->DP . self::$ci->YuiJar;

        return $tag->expand();
    }

    // ------------------------------------------------------------------------

    /**
     * Asset Manager
     *
     * @usage   <ion:AssetManager reference="referenceName (Like : jquery)" src="referencePath">
     *              .....
     *          </ion:AssetManager>
     *
     * @param FTL_Binding $tag
     * @return string
     */
    public static function tag_AssetManager(FTL_Binding $tag)
    {
        $reference = $tag->getAttribute('reference', NULL);
        $src = $tag->getAttribute('src', NULL);

        self::$ci->SourcePath   = self::$ci->ThemePath . $src;
        self::$ci->AssetManager = new Assetic\AssetManager();

        if( !is_null($reference) && file_exists(self::$ci->SourcePath) )
        {
            // Set Asset Reference
            self::$ci->AssetManager->set($reference, new Assetic\Asset\FileAsset(self::$ci->SourcePath));

            self::$ci->AssetReference = new Assetic\Asset\AssetCollection(array(new Assetic\Asset\AssetReference(self::$ci->AssetManager, $reference)));
        }

        return $tag->expand();
    }

    // ------------------------------------------------------------------------

    /**
     * Asset Collection
     *
     * @usage   <ion:AssetCollection>
     *              .....
     *          <ion:AssetCollection>
     *
     * @param FTL_Binding $tag
     * @return string
     */
    public static function tag_AssetCollection(FTL_Binding $tag)
    {
        self::$ci->Collection       = is_null ($tag->getAttribute('collection', NULL)) ? rand() : $tag->getAttribute('collection');
        self::$ci->OutputPath       = self::$ci->ThemePath . $tag->getAttribute('path', NULL);
        self::$ci->OutputURL        = self::$ci->ThemeURL . $tag->getAttribute('path', NULL);

        // Set File Name
        self::setFileName($tag->getAttribute('filename', NULL));

        self::$ci->Method = 'AssetCollection';

        return $tag->expand();
    }



    // ------------------------------------------------------------------------

    /**
     * FileAsset
     *
     * @usage   <ion:FileAsset src="sourceFile" filter="filterMethod" />
     *
     * @param FTL_Binding $tag
     * @return string
     */
    public static function tag_FileAsset(FTL_Binding $tag)
    {
        $src    = $tag->getAttribute('src', NULL);
        $filter = $tag->getAttribute('filter', NULL);

        unset(self::$ci->Filter);

        self::$ci->SourcePath   = self::$ci->ThemePath . $src;

        if( ! is_null($src) && file_exists(self::$ci->SourcePath) )
        {
            self::$ci->Extentions[] = substr(strrchr($src, '.'), 1);

            if( ! is_null($filter) && self::setFilters($filter) ) { }
            else
            {
                self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                    new Assetic\Asset\FileAsset(self::$ci->SourcePath)
                ));
            }
        }
        else
            return self::show_tag_error(
                $tag,
                '"src" is null or file could not found in ' . self::$ci->SourcePath
            );

        return $tag->expand();
    }

    // ------------------------------------------------------------------------

    /**
     * GlobAsset
     *
     * @usage <ion:GlobAsset src="sourcePath" />
     *
     * @param FTL_Binding $tag
     * @return String
     */
    public static function tag_GlobAsset(FTL_Binding $tag)
    {
        $src = $tag->getAttribute('src', NULL);
        $filter = $tag->getAttribute('filter', NULL);

        unset(self::$ci->Filter);

        if( ! is_null($filter) )
            self::$ci->Filter = self::setFilters($filter);

        self::$ci->SourcePath   = self::$ci->ThemePath . $src;

        if( ! is_null($src) && file_exists(self::$ci->SourcePath) || strpos($src, '*.') )
        {
            self::$ci->Extentions[] = substr(strrchr($src, '.'), 1);

            if( ! empty(self::$ci->Filter) ){
                self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                    new Assetic\Asset\GlobAsset(self::$ci->SourcePath, array(self::$ci->Filter))
                ));
            }
            else
            {
                self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                    new Assetic\Asset\GlobAsset(self::$ci->SourcePath)
                ));
            }
        }
        else
            return self::show_tag_error(
                $tag,
                '"src" is null or file could not found in ' . self::$ci->SourcePath
            );


        return $tag->expand();
    }

    /**
     * Compressor Tag
     *
     * @Usage : <ion:Compressor method="" type="" />
     *
     * @param FTL_Binding $tag
     * @return string
     */
    public static function tag_Compressor(FTL_Binding $tag)
    {
        $method = strtolower($tag->getAttribute('method', NULL));
        $type   = strtolower($tag->getAttribute('type', NULL));

        switch($method)
        {
            // Compressor For Javascripts and Stylesheets |- Local "JAR" File Compressor
            case 'yui':

                if( ! file_exists(self::$ci->YuiJarPath) )
                    return self::show_tag_error(
                        $tag,
                        'Yui compressor jar file could not found, if you want yui compressor jar file, download latest yui compressor jar file and put it here : ' . self::$ci->YuiJarPath
                    );

                if( file_exists(self::$ci->YuiJarPath) )
                {
                    foreach(self::$ci->Extentions as $key => $extention)
                    {
                        if($extention == 'js')
                        {
                            self::$ci->Compressor[] = new Assetic\Filter\Yui\JsCompressorFilter(self::$ci->YuiJarPath);
                        }
                        if($extention == 'css')
                        {
                            self::$ci->Compressor[] = new Assetic\Filter\Yui\CssCompressorFilter(self::$ci->YuiJarPath);
                        }
                    }
                }

                break;

            // Compressor For Javascripts  |- Local "JAR" File Compressor -|- Online "API" Compressor
            case 'google':

                if( $type == 'jar' && file_exists(self::$ci->GoogleJarPath) )
                {
                    if( ! file_exists(self::$ci->GoogleJarPath) )
                        return self::show_tag_error(
                            $tag,
                            'Google compiler jar file could not found, if you want google compiler jar file, download latest compiler jar file and put it here : ' . self::$ci->GoogleJarPath
                        );

                    foreach(self::$ci->Extentions as $key => $extention)
                    {
                        if($extention == 'js')
                        {
                            self::$ci->Compressor[] = new Assetic\Filter\GoogleClosure\CompilerJarFilter(self::$ci->GoogleJarPath);
                        }
                    }
                }

                if( $type == 'api' )
                {
                    foreach(self::$ci->Extentions as $key => $extention)
                    {
                        if($extention == 'js')
                        {
                            self::$ci->Compressor[] = new Assetic\Filter\GoogleClosure\CompilerApiFilter();
                        }
                    }
                }
                break;

            // Local Javascript Minify
            case 'jsmin':

                foreach(self::$ci->Extentions as $key => $extention)
                {
                    if($extention == 'js')
                    {
                        self::$ci->Compressor[] = new Assetic\Filter\JSMinFilter(self::$ci->AssetManager);
                    }
                }

                break;

            // Local Javascript Minify
            case 'jsminplus':

                foreach(self::$ci->Extentions as $key => $extention)
                {
                    if($extention == 'js')
                    {
                        self::$ci->Compressor[] = new Assetic\Filter\JSMinPlusFilter(self::$ci->AssetManager);
                    }
                }

                break;

            case 'cssmin':

                foreach(self::$ci->Extentions as $key => $extention)
                {
                    if($extention == 'css')
                    {
                        self::$ci->Compressor[] = new Assetic\Filter\CssMinFilter(self::$ci->AssetManager);
                    }
                }

                break;

            case 'cssrewrite':
                foreach(self::$ci->Extentions as $key => $extention)
                {
                    if($extention == 'css')
                    {
                        self::$ci->Compressor[] = new Assetic\Filter\CssRewriteFilter();
                    }
                }
                break;
        }

        return $tag->expand();
    }

    // ------------------------------------------------------------------------

    /**
     * Render
     *
     * @usage <ion:Render />
     *
     * @param FTL_Binding $tag
     * @return string
     */
    public static function tag_Render(FTL_Binding $tag)
    {
        switch(self::$ci->Method)
        {
            case 'AssetCollection':

                self::$ci->_AssetCollection = new Assetic\Asset\AssetCollection(self::$ci->Collections);

                unset(self::$ci->OutputFileTime);

                if( file_exists(self::$ci->OutputPath . self::$ci->DP . self::$ci->OutputFileName) )
                    self::$ci->OutputFileTime   = filemtime(self::$ci->OutputPath . self::$ci->DP . self::$ci->OutputFileName);
                else
                    self::$ci->OutputFileTime   = 0;

                if( self::$ci->OutputFileTime < self::$ci->_AssetCollection->getLastModified())
                {
                    if( ! empty(self::$ci->Compressor) && ENVIRONMENT == 'production')
                        self::$ci->AssetCollection = new Assetic\Asset\AssetCollection(self::$ci->Collections, self::$ci->Compressor);
                    else
                        self::$ci->AssetCollection = new Assetic\Asset\AssetCollection(self::$ci->Collections);

                    // Check, If we have Path & FileName -> Continue
                    if( ! is_null(self::$ci->OutputPath) && ! is_null(self::$ci->OutputFileName) )
                    {
                        // Check, if Path exist, if Path Writable -> Continue
                        if( file_exists(self::$ci->OutputPath) && is_writable(self::$ci->OutputPath) )
                        {
                            // Set Target Path
                            self::$ci->AssetCollection->setTargetPath(self::$ci->OutputFileName);

                            // Cache Assets
                            self::$ci->Cache = new Assetic\Asset\AssetCache(
                                self::$ci->AssetCollection,
                                new Assetic\Cache\FilesystemCache(self::$ci->CachePath)
                            );

                            // Set Cached data with gived Collection Name
                            self::$ci->AssetManager->set(self::$ci->Collection, self::$ci->Cache);

                            // Write cached assets to a file
                            self::$ci->AssetWriter = new Assetic\AssetWriter(self::$ci->OutputPath);
                            self::$ci->AssetWriter->writeManagerAssets(self::$ci->AssetManager);
                        }
                        else
                            return self::show_tag_error(
                                $tag,
                                'Output path not exist or not writable, check output path ' . self::$ci->OutputPath . self::$ci->OutputFileName
                            );
                    }
                }

                break;

        }

        return $tag->expand();
    }

    // ------------------------------------------------------------------------

    /**
     * AssetURL
     *
     * Return Asset URL or Asset URL With Asset Tag
     *
     * @usage <ion:AssetURL />
     *
     * @param FTL_Binding $tag
     * @return String
     */
    public static function tag_AssetURL(FTL_Binding $tag)
    {
        $url = $tag->getAttribute('url', NULL);

        if( file_exists(self::$ci->OutputPath . self::$ci->DP . self::$ci->OutputFileName) )
        {
            $extention = substr(strrchr(self::$ci->OutputFileName, '.'), 1);

            $result = '';

            self::$ci->OutputFileTime   = filemtime(self::$ci->OutputPath . self::$ci->DP . self::$ci->OutputFileName);
            self::$ci->OutputFileURL    = self::$ci->OutputURL . '/' . self::$ci->OutputFileName . '?v=' . self::$ci->OutputFileTime;

            if( is_null($url) )
            {
                switch($extention)
                {
                    case 'css':
                        $result = '<link rel="stylesheet" href="' . self::$ci->OutputFileURL . '" />';
                        break;

                    case 'js':
                        $result = '<script type="text/javascript" src="' . self::$ci->OutputFileURL . '"></script>';
                        break;

                }
            }
            else
                $result = self::$ci->OutputFileURL;

            return $result;
        }
        else
            return self::show_tag_error(
                $tag,
                'Output file could not found, check write permission for directory ' . self::$ci->OutputPath . self::$ci->DP . self::$ci->OutputFileName
            );
    }

    // ------------------------------------------------------------------------

    /**
     * Set Assetic Filters
     *
     * @param $filter
     * @param null $AssetType
     * @return bool
     */
    private static function setFilters($filter, $AssetType=NULL)
    {
        $AssetType = ! is_null($AssetType) ? $AssetType : 'FileAsset';

        switch($filter)
        {
            case 'callables':
                    switch($AssetType)
                    {
                        case 'FileAsset':
                                self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                    new Assetic\Asset\FileAsset(self::$ci->SourcePath, array(new Assetic\Filter\CallablesFilter()))
                                ));
                                return TRUE;
                            break;

                        case 'GlobAsset':
                                self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                    new Assetic\Asset\GlobAsset(self::$ci->SourcePath, array(new Assetic\Filter\CallablesFilter()))
                                ));
                                return TRUE;
                            break;

                        default:
                                return FALSE;
                            break;
                    }
                break;

            case 'coffee':
                    switch($AssetType)
                    {
                        case 'FileAsset':
                            self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                new Assetic\Asset\FileAsset(self::$ci->SourcePath, array(new Assetic\Filter\CoffeeScriptFilter()))
                            ));
                            return TRUE;
                            break;

                        case 'GlobAsset':
                            self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                new Assetic\Asset\GlobAsset(self::$ci->SourcePath, array(new Assetic\Filter\CoffeeScriptFilter()))
                            ));
                            return TRUE;
                            break;

                        default:
                            return FALSE;
                            break;
                    }
                break;

            case 'compass':
                    switch($AssetType)
                    {
                        case 'FileAsset':
                            self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                new Assetic\Asset\FileAsset(self::$ci->SourcePath, array(new Assetic\Filter\CompassFilter()))
                            ));
                            return TRUE;
                            break;

                        case 'GlobAsset':
                            self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                new Assetic\Asset\GlobAsset(self::$ci->SourcePath, array(new Assetic\Filter\CompassFilter()))
                            ));
                            return TRUE;
                            break;

                        default:
                            return FALSE;
                            break;
                    }
                break;

            case 'less':
                    switch($AssetType)
                    {
                        case 'FileAsset':
                            self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                new Assetic\Asset\FileAsset(self::$ci->SourcePath, array(new Assetic\Filter\LessFilter()))
                            ));
                            return TRUE;
                            break;

                        case 'GlobAsset':
                            self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                new Assetic\Asset\GlobAsset(self::$ci->SourcePath, array(new Assetic\Filter\LessFilter()))
                            ));
                            return TRUE;
                            break;

                        default:
                            return FALSE;
                            break;
                    }
                break;

            case 'lessphp':
                    switch($AssetType)
                    {
                        case 'FileAsset':
                            self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                new Assetic\Asset\FileAsset(self::$ci->SourcePath, array(new Assetic\Filter\LessphpFilter()))
                            ));
                            return TRUE;
                            break;

                        case 'GlobAsset':
                            self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                new Assetic\Asset\GlobAsset(self::$ci->SourcePath, array(new Assetic\Filter\LessphpFilter()))
                            ));
                            return TRUE;
                            break;

                        default:
                            return FALSE;
                            break;
                    }
                break;

            case 'sass':
                    switch($AssetType)
                    {
                        case 'FileAsset':
                            self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                new Assetic\Asset\FileAsset(self::$ci->SourcePath, array(new Assetic\Filter\Sass\SassFilter()))
                            ));
                            return TRUE;
                            break;

                        case 'GlobAsset':
                            self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                new Assetic\Asset\GlobAsset(self::$ci->SourcePath, array(new Assetic\Filter\Sass\SassFilter()))
                            ));
                            return TRUE;
                            break;

                        default:
                            return FALSE;
                            break;
                    }
                break;

            case 'scss':
                    switch($AssetType)
                    {
                        case 'FileAsset':
                            self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                new Assetic\Asset\FileAsset(self::$ci->SourcePath, array(new Assetic\Filter\Sass\ScssFilter()))
                            ));
                            return TRUE;
                            break;

                        case 'GlobAsset':
                            self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                new Assetic\Asset\GlobAsset(self::$ci->SourcePath, array(new Assetic\Filter\Sass\ScssFilter()))
                            ));
                            return TRUE;
                            break;

                        default:
                            return FALSE;
                            break;
                    }
                break;

            case 'sprockets':
                    switch($AssetType)
                    {
                        case 'FileAsset':
                            self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                new Assetic\Asset\FileAsset(self::$ci->SourcePath, array(new Assetic\Filter\SprocketsFilter()))
                            ));
                            return TRUE;
                            break;

                        case 'GlobAsset':
                            self::$ci->Collections[] = new Assetic\Asset\AssetCollection(array(
                                new Assetic\Asset\GlobAsset(self::$ci->SourcePath, array(new Assetic\Filter\SprocketsFilter()))
                            ));
                            return TRUE;
                            break;

                        default:
                            return FALSE;
                            break;
                    }
                break;
            default:
                    return FALSE;
                break;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Set File Name
     *
     * @param null $filename
     */
    function setFileName($filename=NULL)
    {
        if( ! is_null($filename) )
        {
            self::$ci->OutputFileName   = $filename;

            // If ENVIRONMENT is not "production" add ENVIRONMENT to filename
            if(ENVIRONMENT != 'production')
            {
                self::$ci->OutputFileName = ENVIRONMENT . '.' . self::$ci->OutputFileName;
            }
        }

        return;
    }
}