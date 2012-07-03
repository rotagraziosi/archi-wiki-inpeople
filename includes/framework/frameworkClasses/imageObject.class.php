<?php
// classe de gestion des images
// Dorer Laurent 2008

// historique des versions
// version 1.1 --- 15-09-2008 - ajout de la fonction de redimensionnement des images

class imageObject extends config
{
	protected $tabImages;
	
	function __construct()
	{
		parent::__construct();
		$this->tabImages = array();
	}

	// redimensionne un image
	// chemin = chemin de destination
	public function redimension($imageFile='',$imageType='jpg',$chemin='',$newLength=0,$params = array())
	{
		switch($imageType)
		{
			case 'gif':
				$im=imagecreatefromgif($imageFile);
			break;
			
			case 'jpg':
			case 'JPG':
				$im=imagecreatefromjpeg($imageFile);
			break;
			
			case 'png':
				$im=imagecreatefrompng($imageFile);
			break;
			
			default:
				echo 'format d\'image non supporté';
			break;
		}
		
		list($originalWidth, $originalHeight, $type, $attr) = getimagesize($imageFile);
		
		if($originalWidth<$newLength && $originalHeight<$newLength && isset($params['redimOnlyIfFileSizesSuperiorToRedimValue']) && $params['redimOnlyIfFileSizesSuperiorToRedimValue']==true)
		{
			$newWidth = $originalWidth;
			$newHeight = $originalHeight;
		}
		else
		{
			if($newLength==0)
			{
				$newWidth = $originalWidth;
				$newHeight = $originalHeight;
			}
			elseif($originalWidth>$originalHeight)
			{
				$newWidth=$newLength;
				$newHeight     = round($originalHeight * $newWidth / $originalWidth);
			}
			else
			{
				$newHeight = $newLength;
				$newWidth     = round($originalWidth * $newHeight / $originalHeight);
			}
		}
		
		$imDestination = imagecreatetruecolor( $newWidth, $newHeight);
		imagecopyresampled( $imDestination, $im, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
		imagejpeg( $imDestination, $chemin, 100);
		
		imagedestroy($im);
		imagedestroy($imDestination);
	}
	
	
	
	// renvoi les fonctions toolman pour le drag and drop (classement d'images)  => gestion de position d'images ou plus generalement de calque
	public function getJSFunctionsDragAndDrop($params = array())
	{
		$html= "";
		if(isset($params['withBalisesScript']) && $params['withBalisesScript']==true)
		{
			$html.="<script  >";
		}
	
		$html.="
		//
		//	Copyright (c) 2005 Tim Taylor Consulting <http://tool-man.org/>

		//	Permission is hereby granted, free of charge, to any person obtaining a
		//	copy of this software and associated documentation files (the \"Software\"),
		//	to deal in the Software without restriction, including without limitation
		//	the rights to use, copy, modify, merge, publish, distribute, sublicense,
		//	and/or sell copies of the Software, and to permit persons to whom the
		//	Software is furnished to do so, subject to the following conditions:

		//	The above copyright notice and this permission notice shall be included
		//	in all copies or substantial portions of the Software.

		//	THE SOFTWARE IS PROVIDED \"AS IS\", WITHOUT WARRANTY OF ANY KIND, EXPRESS
		//	OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
		//	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
		//	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
		//	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
		//	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
		//	IN THE SOFTWARE.
		

		var ToolMan = {
		  events : function() {
		    if (!ToolMan._eventsFactory) throw \"ToolMan Events module isn't loaded\";
		    return ToolMan._eventsFactory
		  },

		  css : function() {
		    if (!ToolMan._cssFactory) throw \"ToolMan CSS module isn't loaded\";
		    return ToolMan._cssFactory
		  },

		  coordinates : function() {
		    if (!ToolMan._coordinatesFactory) throw \"ToolMan Coordinates module isn't loaded\";
		    return ToolMan._coordinatesFactory
		  },

		  drag : function() {
		    if (!ToolMan._dragFactory) throw \"ToolMan Drag module isn't loaded\";
		    return ToolMan._dragFactory
		  },

		  dragsort : function() {
		    if (!ToolMan._dragsortFactory) throw \"ToolMan DragSort module isn't loaded\";
		    return ToolMan._dragsortFactory
		  },

		  helpers : function() {
		    return ToolMan._helpers
		  },

		  cookies : function() {
		    if (!ToolMan._cookieOven) throw \"ToolMan Cookie module isn't loaded\";
		    return ToolMan._cookieOven
		  },

		  junkdrawer : function() {
		    return ToolMan._junkdrawer
		  }

		}

		ToolMan._helpers = {
		  map : function(array, func) {
		    for (var i = 0, n = array.length; i < n; i++) func(array[i])
		  },

		  nextItem : function(item, nodeName) {
		    if (item == null) return
		    var next = item.nextSibling
		    while (next != null) {
		      if (next.nodeName == nodeName) return next
		      next = next.nextSibling
		    }
		    return null
		  },

		  previousItem : function(item, nodeName) {
		    var previous = item.previousSibling
		    while (previous != null) {
		      if (previous.nodeName == nodeName) return previous
		      previous = previous.previousSibling
		    }
		    return null
		  },

		  moveBefore : function(item1, item2) {
		    var parent = item1.parentNode
		    parent.removeChild(item1)
		    parent.insertBefore(item1, item2)
		  },

		  moveAfter : function(item1, item2) {
		    var parent = item1.parentNode
		    parent.removeChild(item1)
		    parent.insertBefore(item1, item2 ? item2.nextSibling : null)
		  }
		}

		// 
		// scripts without a proper home
		//
		// stuff here is subject to change unapologetically and without warning
		//
		ToolMan._junkdrawer = {
		  serializeList : function(list) {
		    var items = list.getElementsByTagName(\"li\")
		    var array = new Array()
		    for (var i = 0, n = items.length; i < n; i++) {
		      var item = items[i]

		      array.push(ToolMan.junkdrawer()._identifier(item))
		    }
		    return array.join('|')
		  },

		  inspectListOrder : function(id) {
		    alert(ToolMan.junkdrawer().serializeList(document.getElementById(id)))
		  },

		    inspectListOrderToId : function(id,idFormElement) {
		    //alert(ToolMan.junkdrawer().serializeList(document.getElementById(id)))
			document.getElementById(idFormElement).value=ToolMan.junkdrawer().serializeList(document.getElementById(id));
		  }, 
		  
		  
		  restoreListOrder : function(listID) {
		    var list = document.getElementById(listID)
		    if (list == null) return

		    var cookie = ToolMan.cookies().get(\"list-\" + listID)
		    if (!cookie) return;

		    var IDs = cookie.split('|')
		    var items = ToolMan.junkdrawer()._itemsByID(list)

		    for (var i = 0, n = IDs.length; i < n; i++) {
		      var itemID = IDs[i]
		      if (itemID in items) {
		        var item = items[itemID]
		        list.removeChild(item)
		        list.insertBefore(item, null)
		      }
		    }
		  },

		  _identifier : function(item) {
		    var trim = ToolMan.junkdrawer().trim
		    var identifier

		    identifier = trim(item.getAttribute(\"id\"))
		    if (identifier != null && identifier.length > 0) return identifier;
		    
		    identifier = trim(item.getAttribute(\"itemID\"))
		    if (identifier != null && identifier.length > 0) return identifier;
		    
		    // FIXME: strip out special chars or make this an MD5 hash or something
		    return trim(item.innerHTML)
		  },

		  _itemsByID : function(list) {
		    var array = new Array()
		    var items = list.getElementsByTagName('li')
		    for (var i = 0, n = items.length; i < n; i++) {
		      var item = items[i]
		      array[ToolMan.junkdrawer()._identifier(item)] = item
		    }
		    return array
		  },

		  trim : function(text) {
		    if (text == null) return null
		    return text.replace(/^(\s+)?(.*\S)(\s+)?$/, '$2')
		  }
		}


		//events.js

		// Copyright (c) 2005 Tim Taylor Consulting (see LICENSE.txt) 

		ToolMan._eventsFactory = {
		  fix : function(event) {
		    if (!event) event = window.event

		    if (event.target) {
		      if (event.target.nodeType == 3) event.target = event.target.parentNode
		    } else if (event.srcElement) {
		      event.target = event.srcElement
		    }

		    return event
		  },

		  register : function(element, type, func) {
		    if (element.addEventListener) {
		      element.addEventListener(type, func, false)
		    } else if (element.attachEvent) {
		      if (!element._listeners) element._listeners = new Array()
		      if (!element._listeners[type]) element._listeners[type] = new Array()
		      var workaroundFunc = function() {
		        func.apply(element, new Array())
		      }
		      element._listeners[type][func] = workaroundFunc
		      element.attachEvent('on' + type, workaroundFunc)
		    }
		  },

		  unregister : function(element, type, func) {
		    if (element.removeEventListener) {
		      element.removeEventListener(type, func, false)
		    } else if (element.detachEvent) {
		      if (element._listeners 
		          && element._listeners[type] 
		          && element._listeners[type][func]) {

		        element.detachEvent('on' + type, 
		            element._listeners[type][func])
		      }
		    }
		  }
}


		//css.js

		// Copyright (c) 2005 Tim Taylor Consulting (see LICENSE.txt)

		// TODO: write unit tests
		ToolMan._cssFactory = {
				  readStyle : function(element, property) {
				    if (element.style[property]) {
				      return element.style[property]
				    } else if (element.currentStyle) {
				      return element.currentStyle[property]
				    } else if (document.defaultView && document.defaultView.getComputedStyle) {
				      var style = document.defaultView.getComputedStyle(element, null)
				      return style.getPropertyValue(property)
				    } else {
				      return null
				    }
				  }
		}


		//coordinates.js

		// Copyright (c) 2005 Tim Taylor Consulting (see LICENSE.txt)

		// FIXME: assumes position styles are specified in 'px'

		ToolMan._coordinatesFactory = {

		  create : function(x, y) {
		    // FIXME: Safari won't parse 'throw' and aborts trying to do anything with this file
		    //if (isNaN(x) || isNaN(y)) throw \"invalid x,y: \" + x + \",\" + y
		    return new _ToolManCoordinate(this, x, y)
		  },

		  origin : function() {
		    return this.create(0, 0)
		  },

		  //
		  // FIXME: Safari 1.2, returns (0,0) on absolutely positioned elements
		  
		  topLeftPosition : function(element) {
		    var left = parseInt(ToolMan.css().readStyle(element, \"left\"))
		    var left = isNaN(left) ? 0 : left
		    var top = parseInt(ToolMan.css().readStyle(element, \"top\"))
		    var top = isNaN(top) ? 0 : top

		    return this.create(left, top)
		  },

		  bottomRightPosition : function(element) {
		    return this.topLeftPosition(element).plus(this._size(element))
		  },

		  topLeftOffset : function(element) {
		    var offset = this._offset(element) 

		    var parent = element.offsetParent
		    while (parent) {
		      offset = offset.plus(this._offset(parent))
		      parent = parent.offsetParent
		    }
		    return offset
		  },

		  bottomRightOffset : function(element) {
		    return this.topLeftOffset(element).plus(
		        this.create(element.offsetWidth, element.offsetHeight))
		  },

		  scrollOffset : function() {
		    if (window.pageXOffset) {
		      return this.create(window.pageXOffset, window.pageYOffset)
		    } else if (document.documentElement) {
		      return this.create(
		          document.body.scrollLeft + document.documentElement.scrollLeft, 
		          document.body.scrollTop + document.documentElement.scrollTop)
		    } else if (document.body.scrollLeft >= 0) {
		      return this.create(document.body.scrollLeft, document.body.scrollTop)
		    } else {
		      return this.create(0, 0)
		    }
		  },

		  clientSize : function() {
		    if (window.innerHeight >= 0) {
		      return this.create(window.innerWidth, window.innerHeight)
		    } else if (document.documentElement) {
		      return this.create(document.documentElement.clientWidth,
		          document.documentElement.clientHeight)
		    } else if (document.body.clientHeight >= 0) {
		      return this.create(document.body.clientWidth,
		          document.body.clientHeight)
		    } else {
		      return this.create(0, 0)
		    }
		  },

		  //
		  // mouse coordinate relative to the window (technically the
		   // browser client area) i.e. the part showing your page
		   //
		   // NOTE: in Safari the coordinate is relative to the document
		   //
		  mousePosition : function(event) {
		    event = ToolMan.events().fix(event)
		    return this.create(event.clientX, event.clientY)
		  },

		  //
		   // mouse coordinate relative to the document
		   //
		  mouseOffset : function(event) {
		    event = ToolMan.events().fix(event)
		    if (event.pageX >= 0 || event.pageX < 0) {
		      return this.create(event.pageX, event.pageY)
		    } else if (event.clientX >= 0 || event.clientX < 0) {
		      return this.mousePosition(event).plus(this.scrollOffset())
		    }
		  },

		  _size : function(element) {
		  // TODO: move to a Dimension class */
		    return this.create(element.offsetWidth, element.offsetHeight)
		  },

		  _offset : function(element) {
		    return this.create(element.offsetLeft, element.offsetTop)
		  }
}

		function _ToolManCoordinate(factory, x, y) {
		  this.factory = factory
		  this.x = isNaN(x) ? 0 : x
		  this.y = isNaN(y) ? 0 : y
		}

		_ToolManCoordinate.prototype = {
		  toString : function() {
		    return \"(\" + this.x + \",\" + this.y + \")\"
		  },

		  plus : function(that) {
		    return this.factory.create(this.x + that.x, this.y + that.y)
		  },

		  minus : function(that) {
		    return this.factory.create(this.x - that.x, this.y - that.y)
		  },

		  min : function(that) {
		    return this.factory.create(
		        Math.min(this.x , that.x), Math.min(this.y , that.y))
		  },

		  max : function(that) {
		    return this.factory.create(
		        Math.max(this.x , that.x), Math.max(this.y , that.y))
		  },

		  constrainTo : function (one, two) {
		    var min = one.min(two)
		    var max = one.max(two)

		    return this.max(min).min(max)
		  },

		  distance : function (that) {
		    return Math.sqrt(Math.pow(this.x - that.x, 2) + Math.pow(this.y - that.y, 2))
		  },

		  reposition : function(element) {
		    element.style[\"top\"] = this.y + \"px\"
		    element.style[\"left\"] = this.x + \"px\"
		  }
		}


		// drag.js

		// Copyright (c) 2005 Tim Taylor Consulting (see LICENSE.txt)

		ToolMan._dragFactory = {
		  createSimpleGroup : function(element, handle) {
		    handle = handle ? handle : element
		    var group = this.createGroup(element)
		    group.setHandle(handle)
		    group.transparentDrag()
		    group.onTopWhileDragging()
		    return group
		  },

		  createGroup : function(element) {
		    var group = new _ToolManDragGroup(this, element)

		    var position = ToolMan.css().readStyle(element, 'position')
		    if (position == 'static') {
		      element.style[\"position\"] = 'relative'
		    } else if (position == 'absolute') {
		      // for Safari 1.2
		      ToolMan.coordinates().topLeftOffset(element).reposition(element)
		    }

		    // TODO: only if ToolMan.isDebugging()
		    group.register('draginit', this._showDragEventStatus)
		    group.register('dragmove', this._showDragEventStatus)
		    group.register('dragend', this._showDragEventStatus)

		    return group
		  },

		  _showDragEventStatus : function(dragEvent) {
		    window.status = dragEvent.toString()
		  },

		  constraints : function() {
		    return this._constraintFactory
		  },

		  _createEvent : function(type, event, group) {
		    return new _ToolManDragEvent(type, event, group)
		  }
		}

		function _ToolManDragGroup(factory, element) {
		  this.factory = factory
		  this.element = element
		  this._handle = null
		  this._thresholdDistance = 0
		  this._transforms = new Array()
		  // TODO: refactor into a helper object, move into events.js
		  this._listeners = new Array()
		  this._listeners['draginit'] = new Array()
		  this._listeners['dragstart'] = new Array()
		  this._listeners['dragmove'] = new Array()
		  this._listeners['dragend'] = new Array()
		}

		_ToolManDragGroup.prototype = {
		  //
		   // TODO:
		   //  - unregister(type, func)
		  //   - move custom event listener stuff into Event library
		  //   - keyboard nudging of \"selected\" group
		  //

		  setHandle : function(handle) {
		    var events = ToolMan.events()

		    handle.toolManDragGroup = this
		    events.register(handle, 'mousedown', this._dragInit)
		    handle.onmousedown = function() { return false }

		    if (this.element != handle)
		      events.unregister(this.element, 'mousedown', this._dragInit)
		  },

		  register : function(type, func) {
		    this._listeners[type].push(func)
		  },

		  addTransform : function(transformFunc) {
		    this._transforms.push(transformFunc)
		  },

		  verticalOnly : function() {
		    this.addTransform(this.factory.constraints().vertical())
		  },

		  horizontalOnly : function() {
		    this.addTransform(this.factory.constraints().horizontal())
		  },

		  setThreshold : function(thresholdDistance) {
		    this._thresholdDistance = thresholdDistance
		  },

		  transparentDrag : function(opacity) {
		    var opacity = typeof(opacity) != \"undefined\" ? opacity : 0.75;
		    var originalOpacity = ToolMan.css().readStyle(this.element, \"opacity\")

		    this.register('dragstart', function(dragEvent) {
		      var element = dragEvent.group.element
		      element.style.opacity = opacity
		      element.style.filter = 'alpha(opacity=' + (opacity * 100) + ')'
		    })
		    this.register('dragend', function(dragEvent) {
		      var element = dragEvent.group.element
		      element.style.opacity = originalOpacity
		      element.style.filter = 'alpha(opacity=100)'
		    })
		  },

		  onTopWhileDragging : function(zIndex) {
		    var zIndex = typeof(zIndex) != \"undefined\" ? zIndex : 100000;
		    var originalZIndex = ToolMan.css().readStyle(this.element, \"z-index\")

		    this.register('dragstart', function(dragEvent) {
		      dragEvent.group.element.style.zIndex = zIndex
		    })
		    this.register('dragend', function(dragEvent) {
		      dragEvent.group.element.style.zIndex = originalZIndex
		    })
		  },

		  _dragInit : function(event) {
		    event = ToolMan.events().fix(event)
		    var group = document.toolManDragGroup = this.toolManDragGroup
		    var dragEvent = group.factory._createEvent('draginit', event, group)

		    group._isThresholdExceeded = false
		    group._initialMouseOffset = dragEvent.mouseOffset
		    group._grabOffset = dragEvent.mouseOffset.minus(dragEvent.topLeftOffset)
		    ToolMan.events().register(document, 'mousemove', group._drag)
		    document.onmousemove = function() { return false }
		    ToolMan.events().register(document, 'mouseup', group._dragEnd)

		    group._notifyListeners(dragEvent)
		  },

		  _drag : function(event) {
		    event = ToolMan.events().fix(event)
		    var coordinates = ToolMan.coordinates()
		    var group = this.toolManDragGroup
		    if (!group) return
		    var dragEvent = group.factory._createEvent('dragmove', event, group)

		    var newTopLeftOffset = dragEvent.mouseOffset.minus(group._grabOffset)

		    // TODO: replace with DragThreshold object
		    if (!group._isThresholdExceeded) {
		      var distance = 
		          dragEvent.mouseOffset.distance(group._initialMouseOffset)
		      if (distance < group._thresholdDistance) return
		      group._isThresholdExceeded = true
		      group._notifyListeners(
		          group.factory._createEvent('dragstart', event, group))
		    }

		    for (i in group._transforms) {
		      var transform = group._transforms[i]
		      newTopLeftOffset = transform(newTopLeftOffset, dragEvent)
		    }

		    var dragDelta = newTopLeftOffset.minus(dragEvent.topLeftOffset)
		    var newTopLeftPosition = dragEvent.topLeftPosition.plus(dragDelta)
		    newTopLeftPosition.reposition(group.element)
		    dragEvent.transformedMouseOffset = newTopLeftOffset.plus(group._grabOffset)

		    group._notifyListeners(dragEvent)

		    var errorDelta = newTopLeftOffset.minus(coordinates.topLeftOffset(group.element))
		    if (errorDelta.x != 0 || errorDelta.y != 0) {
		      coordinates.topLeftPosition(group.element).plus(errorDelta).reposition(group.element)
		    }
		  },

		  _dragEnd : function(event) {
		    event = ToolMan.events().fix(event)
		    var group = this.toolManDragGroup
		    var dragEvent = group.factory._createEvent('dragend', event, group)

		    group._notifyListeners(dragEvent)

		    this.toolManDragGroup = null
		    ToolMan.events().unregister(document, 'mousemove', group._drag)
		    document.onmousemove = null
		    ToolMan.events().unregister(document, 'mouseup', group._dragEnd)
		  },

		  _notifyListeners : function(dragEvent) {
		    var listeners = this._listeners[dragEvent.type]
		    for (i in listeners) {
		      listeners[i](dragEvent)
		    }
		  }
		}

		function _ToolManDragEvent(type, event, group) {
		  this.type = type
		  this.group = group
		  this.mousePosition = ToolMan.coordinates().mousePosition(event)
		  this.mouseOffset = ToolMan.coordinates().mouseOffset(event)
		  this.transformedMouseOffset = this.mouseOffset
		  this.topLeftPosition = ToolMan.coordinates().topLeftPosition(group.element)
		  this.topLeftOffset = ToolMan.coordinates().topLeftOffset(group.element)
		}

		_ToolManDragEvent.prototype = {
		  toString : function() {
		    return \"mouse: \" + this.mousePosition + this.mouseOffset + \"    \" +
		        \"xmouse: \" + this.transformedMouseOffset + \"    \" +
		        \"left,top: \" + this.topLeftPosition + this.topLeftOffset
		  }
		}

		ToolMan._dragFactory._constraintFactory = {
		  vertical : function() {
		    return function(coordinate, dragEvent) {
		      var x = dragEvent.topLeftOffset.x
		      return coordinate.x != x
		          ? coordinate.factory.create(x, coordinate.y) 
		          : coordinate
		    }
		  },

		  horizontal : function() {
		    return function(coordinate, dragEvent) {
		      var y = dragEvent.topLeftOffset.y
		      return coordinate.y != y
		          ? coordinate.factory.create(coordinate.x, y) 
		          : coordinate
		    }
		  }
		}


		/* Copyright (c) 2005 Tim Taylor Consulting (see LICENSE.txt) */

		ToolMan._dragsortFactory = {
		  makeSortable : function(item) {
		    var group = ToolMan.drag().createSimpleGroup(item)

		    group.register('dragstart', this._onDragStart)
		    group.register('dragmove', this._onDragMove)
		    group.register('dragend', this._onDragEnd)

		    return group
		  },

		  // 
		   // Iterates over a list's items, making them sortable, applying
		   // optional functions to each item.
		  //
		   // example: makeListSortable(myList, myFunc1, myFunc2, ... , myFuncN)
		   //
		  makeListSortable : function(list) {
		    var helpers = ToolMan.helpers()
		    var coordinates = ToolMan.coordinates()
		    var items = list.getElementsByTagName(\"li\")

		    helpers.map(items, function(item) {
		      var dragGroup = dragsort.makeSortable(item)
		      dragGroup.setThreshold(4)
		      var min, max
		      dragGroup.addTransform(function(coordinate, dragEvent) {
		        return coordinate.constrainTo(min, max)
		      })
		      dragGroup.register('dragstart', function() {
		        var items = list.getElementsByTagName(\"li\")
		        min = max = coordinates.topLeftOffset(items[0])
		        for (var i = 1, n = items.length; i < n; i++) {
		          var offset = coordinates.topLeftOffset(items[i])
		          min = min.min(offset)
		          max = max.max(offset)
		        }
		      })
		    })
		    for (var i = 1, n = arguments.length; i < n; i++)
		      helpers.map(items, arguments[i])
		  },

		  _onDragStart : function(dragEvent) {
		  },

		  _onDragMove : function(dragEvent) {
		    var helpers = ToolMan.helpers()
		    var coordinates = ToolMan.coordinates()

		    var item = dragEvent.group.element
		    var xmouse = dragEvent.transformedMouseOffset
		    var moveTo = null

		    var previous = helpers.previousItem(item, item.nodeName)
		    while (previous != null) {
		      var bottomRight = coordinates.bottomRightOffset(previous)
		      if (xmouse.y <= bottomRight.y && xmouse.x <= bottomRight.x) {
		        moveTo = previous
		      }
		      previous = helpers.previousItem(previous, item.nodeName)
		    }
		    if (moveTo != null) {
		      helpers.moveBefore(item, moveTo)
		      return
		    }

		    var next = helpers.nextItem(item, item.nodeName)
		    while (next != null) {
		      var topLeft = coordinates.topLeftOffset(next)
		      if (topLeft.y <= xmouse.y && topLeft.x <= xmouse.x) {
		        moveTo = next
		      }
		      next = helpers.nextItem(next, item.nodeName)
		    }
		    if (moveTo != null) {
		      helpers.moveBefore(item, helpers.nextItem(moveTo, item.nodeName))
		      return
		    }
		  },

		  _onDragEnd : function(dragEvent) {
		    ToolMan.coordinates().create(0, 0).reposition(dragEvent.group.element)
		  }
		}

		var ESCAPE = 27
		var ENTER = 13
		var TAB = 9

		var coordinates = ToolMan.coordinates()
		var dragsort = ToolMan.dragsort()



		function setHandle(item) {
				  item.toolManDragGroup.setHandle(findHandle(item))
		}

		function findHandle(item) {
				  var children = item.getElementsByTagName(\"div\")
				  for (var i = 0; i < children.length; i++) {
				    var child = children[i]

				    if (child.getAttribute(\"class\") == null) continue

				    if (child.getAttribute(\"class\").indexOf(\"handle\") >= 0)
				      return child
				  }
				  return item
		}

		function join(name, isDoubleClick) {
		  var view = document.getElementById(name + \"View\")
		  view.editor = document.getElementById(name + \"Edit\")

		  var showEditor = function(event) {
		    event = fixEvent(event)

		    var view = this
		    var editor = view.editor

		    if (!editor) return true

		    if (editor.currentView != null) {
		      editor.blur()
		    }
		    editor.currentView = view

		    var topLeft = coordinates.topLeftOffset(view)
		    topLeft.reposition(editor)
		    if (editor.nodeName == 'TEXTAREA') {
		      editor.style['width'] = view.offsetWidth + \"px\"
		      editor.style['height'] = view.offsetHeight + \"px\"
		    }
		    editor.value = view.innerHTML
		    editor.style['visibility'] = 'visible'
		    view.style['visibility'] = 'hidden'
		    editor.focus()
		    return false
		  }

		  if (isDoubleClick) {
		    view.ondblclick = showEditor
		  } else {
		    view.onclick = showEditor
		  }

		  view.editor.onblur = function(event) {
		    event = fixEvent(event)

		    var editor = event.target
		    var view = editor.currentView

		    if (!editor.abandonChanges) view.innerHTML = editor.value
		    editor.abandonChanges = false
		    editor.style['visibility'] = 'hidden'
		    editor.value = '' // fixes firefox 1.0 bug
		    view.style['visibility'] = 'visible'
		    editor.currentView = null

		    return true
		  }
		  
		  view.editor.onkeydown = function(event) {
		    event = fixEvent(event)
		    
		    var editor = event.target
		    if (event.keyCode == TAB) {
		      editor.blur()
		      return false
		    }
		  }

		  view.editor.onkeyup = function(event) {
		    event = fixEvent(event)

		    var editor = event.target
		    if (event.keyCode == ESCAPE) {
		      editor.abandonChanges = true
		      editor.blur()
		      return false
		    } else if (event.keyCode == TAB) {
		      return false
		    } else {
		      return true
		    }
		  }

		  // TODO: this method is duplicated elsewhere
		  function fixEvent(event) {
		    if (!event) event = window.event
		    if (event.target) {
		      if (event.target.nodeType == 3) event.target = event.target.parentNode
		    } else if (event.srcElement) {
		      event.target = event.srcElement
		    }

		    return event
		  }
		}

					function verticalOnly(item) {
				item.toolManDragGroup.verticalOnly()
			}
			ToolMan._cookieOven = {

			set : function(name, value, expirationInDays) {
				if (expirationInDays) {
					var date = new Date()
					date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000))
					var expires = '; expires=' + date.toGMTString()
				} else {
					var expires = ''
				}
				document.cookie = name + '=' + value + expires + '; path=/'
			},

			get : function(name) {
				var namePattern = name + '='
				var cookies = document.cookie.split(';')
				for(var i = 0, n = cookies.length; i < n; i++) {
					var c = cookies[i]
					while (c.charAt(0) == ' ') c = c.substring(1, c.length)
					if (c.indexOf(namePattern) == 0)
						return c.substring(namePattern.length, c.length)
				}
				return null
			},

			eraseCookie : function(name) {
				createCookie(name, '', -1)
			}
		}

			function speak(id, what) {
				var element = document.getElementById(id);
				element.innerHTML = 'Clicked ' + what;
			}

			function saveOrder(item) {
				var group = item.toolManDragGroup
				var list = group.element.parentNode
				var id = list.getAttribute('id')
				if (id == null) return
				group.register('dragend', function() {
					ToolMan.cookies().set('list-' + id, 
							junkdrawer.serializeList(list), 365)
				})
			}

			";
			
		if(isset($params['withBalisesScript']) && $params['withBalisesScript']==true)
		{
			$html.="</script>";
		}
			
			
			
		return $html;
	}
	
	// ajoute une image a la liste d'images
	public function addImageDragAndDrop($params=array())
	{
		$this->tabImages[] = $params;
	}
	
	// renvoi l'affichage des images dans les balise ul et li pour le tri par dragAndDrop
	// une fois le formulaire validé , il faut lire le POST['listeDragAndDrop'] qui contient la liste des elements dans l'ordre qui a ete choisi par l'utilisateur
	public function getDragAndDrop()
	{
		$html="<ul id='slideshow0' style='width:750px;'>";
		
		foreach($this->tabImages as $indice => $value)
		{
			$html.="<li itemID='".$value['idHistoriqueImage']."' style='float:left; list-style-type: none;'><table border=0 cellpadding=0 cellspacing=0 style='padding-bottom:0px;' width=173 height=150><tr><td align='center' valign='top'>";
			$html.="<img style='padding-left:5px;padding-right:5px;' width=170 src=\"".$value['imageSrc']."?".rand(10000,99999)."\" alt=\"image\"/>";
			$html.="</td></tr></table>";
			$html.="</li>";
		}
		
		$html.="</ul>";
		$html.="<script  >
		var junkdrawer = ToolMan.junkdrawer();
		junkdrawer.restoreListOrder('slideshow0');
		dragsort.makeListSortable(document.getElementById('slideshow0'), saveOrder);
		</script>";
		// champ caché dans lequel on va lire l'ordre des elements
		$html.="<input type='hidden' name='listeDragAndDrop' id='listeDragAndDrop' value=''>";
		
		return $html;
	}
	
	// fonctions d'initialisation a placer a l'interieur et a la fin du formulaire
	public function getJSInitAfterListDragAndDrop($slideName='slideshow0',$params = array())
	{
		
		if(!isset($params['onlyHiddenFormFieldCode']) || $params['onlyHiddenFormField']==false)
		{
			$html="<script  >";
			$html.="var junkdrawer = ToolMan.junkdrawer();dragsort.makeListSortable(document.getElementById('$slideName'), saveOrder);";//junkdrawer.restoreListOrder('$slideName');
			$html.="</script>";
		}
		
		if(!isset($params['onlyJavascriptInitCode']) || $params['onlyJavascriptInitCode']==false)
		{
			// champ caché dans lequel on va lire l'ordre des elements
			$html.="<input type='hidden' name='listeDragAndDrop' id='listeDragAndDrop' value=''>";
		}
		return $html;
	}
	
	// code a placer sur le onclick du bouton du formulaire pour que les ordres des images soient bien valident
	public function getJSSubmitDragAndDrop($slideName='slideshow0')
	{
		return "junkdrawer.inspectListOrderToId('$slideName','listeDragAndDrop');";
	}
	
	
	
	
	// ************************************************************************************************************************************************************************************************************
	// renvoi la liste des identifiants d'images
	public function getArrayFromPostDragAndDrop()
	{
		$retour = array();
		if(isset($this->variablesPost['listeDragAndDrop']) && $this->variablesPost['listeDragAndDrop']!='')
		{
			$listeArray = explode('|',$this->variablesPost['listeDragAndDrop']);
			
			$i=1;
			foreach($listeArray as $indice => $value)
			{
				if(isset($value) && $value<>NULL)
				{
					$retour[$i]=$value;
					$i++;
				}
			}
			
		}
		return $retour;
	}
	
	// *******************************************************************************************************************
	// permet de creer un formulaire pour classer des elements
	// mise en place :
	//*************** 1 *****************
	// 		$listeTriableObject = new imageObject(); // dans l'objet image , il y a une fonction qui permet de creer des listes triables par drag and drop
	// 		$html.="<script  >".$listeTriableObject->getJSFunctionsDragAndDrop()."</script>";
	// 		$i = 0;
	// 		$arrayListeEtapes = array();
	// 		while($fetchListeEtapes = mysql_fetch_assoc($resListeEtapes))
	// 		{
	// 			$arrayListeEtapes[$i]['idEtape'] = array('value'=>$fetchListeEtapes['idEtape'],'type'=>'identifiant');
	// 			$arrayListeEtapes[$i]['&nbsp;'] = array('value'=>"<a href='".$this->creerUrl('','etapesParcoursFormulaire',array('archiIdEtape'=>$fetchListeEtapes['idEtape'],'archiIdParcours'=>$idParcours))."'>".$fetchListeEtapes['idEtape']."</a>",'type'=>'free','widthColonne'=>50);
	// 			$arrayListeEtapes[$i]['libelle'] = array('value'=>$fetchListeEtapes['libelleEtape'],'type'=>'free','widthColonne'=>300);
	// 			$arrayListeEtapes[$i]['adresse'] = array('value'=>$a->getIntituleAdresseFrom($fetchListeEtapes['idEvenementGroupeAdresse'],'idEvenementGroupeAdresse'),'type'=>'free','widthColonne'=>400);
	// 			$i++;
	// 		}
	// 		
	// 		$html .= $listeTriableObject->createSortableFormListeFromArray($arrayListeEtapes);
	//***********************************
	// ensuite 
	// appeler la fonction suivante sur le onclick du bouton de validation du formulaire:
	// $listeTriableObject->getJSSubmitDragAndDrop()
	// ***********************************
	// enfin , cette fonction est a placer a l'interieur et a la fin du formulaire (apres affichage des elements drag&droppables)
	// $listeTriableObject->getJSInitAfterListDragAndDrop()
	// ***********************************
	// quand le formulaire est validé on recupere le champ post listeDragAndDrop qui contient la liste des idenfiant dans l'ordre voulu
	public function createSortableFormListeFromArray($listeArray=array(),$params=array())
	{
		$html="";
		$slideName='';
		if(count($listeArray)>0)
		{
			$styleTable ="style='padding:0;margin:0;'";
			if(isset($params['styleTable']))
			{
				$styleTable = $params['styleTable'];
			}
			
			
			
			if(!isset($params['noEntetesTableau']) || $params['noEntetesTableau']==false)// empeche l'affichage des entetes
			{
				$html.="<table ".$styleTable." border=0>";
				if(isset($params['styleEntete']) && $params['styleEntete']!='')
					$html.="<tr style=\"".$params['styleEntete']."\">";
				else
					$html.="<tr>";
			}
			
			$indiceIdentifiant=""; // recuperation de l'indice de la colonne qui va servir a recuperer les identifiants d'une ligne du tableau affiche
			
			// affichage des entetes
			foreach($listeArray[0] as $identifiantColonne => $val)
			{
				if($val['type']!='identifiant') // on affiche pas la colonne de type "identifiant"
				{
					if(!isset($params['noEntetesTableau']) || $params['noEntetesTableau']==false) // empeche l'affichage des entetes
					{
						$widthColonne = "";
						if(isset($listeArray[0][$identifiantColonne]['widthColonne']))
						{
							$widthColonne = $listeArray[0][$identifiantColonne]['widthColonne'];
						}
						
						$html.="<td width='".$widthColonne."'>".$identifiantColonne."</td>";
					}
				}
				else
				{
					$indiceIdentifiant = $identifiantColonne;
				}
			}
			
			
			if(!isset($params['noEntetesTableau']) || $params['noEntetesTableau']==false)// empeche l'affichage des entetes
			{
				$html.="</tr></table>";
			}
			
			$html.="<table cellpadding=0 cellspacing=0 border=0 style='padding:0;margin:0;'>";
			$html.="<tr><td style='margin:0;padding:0;'>";
			
			
			if(isset($params['slideName']))
			{
				$slideName = $params['slideName'];
			}
			else
			{
				$slideName = "slideshow0";
			}
			
			
			$html.="<ul id='$slideName' style='padding-left:0px;'>";
			
			// parcours des lignes
			for($i = 0 ; $i<count($listeArray);$i++)
			{
				$valeur = $listeArray[$i];
				
				if($indiceIdentifiant!='')
				{
					$styleLi = "";
					if(isset($params['orientation']) && $params['orientation']=='vertical')
					{
						$styleLi = "float:left;";
					}
					$html.="<li itemID='".$valeur[$indiceIdentifiant]['value']."' style='$styleLi list-style-type: none;padding-left:0px;padding-bottom:0px;margin-bottom:0px;'>";
				}
				$html.="<table border=0 style='margin:0;'><tr>";
				
				// parcours des colonnes
				foreach($valeur as $nomColonne => $configColonne)
				{
					if($configColonne['type']!='identifiant')//
					{
						$widthColonne = "";
						if(isset($configColonne['widthColonne']))
						{
							$widthColonne = " width='".$configColonne['widthColonne']."'";
						}
						
						$styleColonneDonnees="";
						if(isset($configColonne['styleColonneDonnees']))
						{
							$styleColonneDonnees = $configColonne['styleColonneDonnees'];
						}
						
						$html.="<td ".$widthColonne." style=\"".$styleColonneDonnees."\">";
						switch($configColonne['type'])
						{
							case 'image':
								$html.="<img style=\"".$configColonne['style']."margin:0px;padding:0px;\" src='".$params['cheminImages']."/".$configColonne['value']."'>";
							break;
							case 'libelle':
								$html.=$configColonne['value'];
							break;
							case 'bigText':
								$bigText = "<textarea onclick=\"this.focus();\" name='".$configColonne['name']."' style=\"".$configColonne['style']."\">".$configColonne['value']."</textarea>";
								$html.=$bigText;
							break;
							case 'checkbox':
								$html.="<input type='checkbox' name='".$configColonne['name']."' value='".$configColonne['value']."' ";
								if(isset($configColonne['checked']) && $configColonne['checked']==true)
								{
									$html.="CHECKED";
								}
								$html.=">";
							break;
							case 'free':
								$html.=$configColonne['value'];
							break;
						}
						$html.="</td>";
					}
				}
				$html.="<tr></table>";
				if($indiceIdentifiant!='')
				{
					$html.="</li>";
				}

			}
			$html.="</ul>";
			$html.="</td></tr></table>";
		}
		
		
		//$html.=$this->getJSInitAfterListDragAndDrop($slideName);
		return $html;
	}
	
	// fonction permettant de redimensionner une image en précisant les hauteurs et largeurs max ainsi que le fichier de destination, si AvantApres est a false , on ajoute un coutour gris a l'image (inutile) , cette fonction permet aussi d'ajouter une signature a l'image
	public function redimensionAvecSignature($imgfile, $maxwidth, $maxheight, $fichierDestination='',$isAvantApres=true,$txt="www.pia.com.fr") 
	{
		/*$pos = pia_strrpos($fichierDestination,'/');
		$nomFichier = pia_substr($fichierDestination,$pos+1,pia_strlen($fichierDestination));
		$fichierDestination='/home/laurent/public_html/pia/tempImages/'.rand(0,10000).$nomFichier; // a supprimer
		echo $fichierDestination;
		*/
		//putenv('GDFONTPATH=' . TTF_DIR_);
		$font = $this->cheminPhysique."/DejaVuSerif.ttf";

		$imgo = imagecreatefromjpeg($imgfile);
		// recuperation des dimensions de l'image originale
		$widtho  = imagesx($imgo);
		$heighto = imagesy($imgo);
		
		if($maxwidth==0 && $maxheight==0) // si maxwidth = 0 et maxheight = 0 on ne redimensionne pas
		{
			$maxwidth=$widtho;
			$maxheight = $heighto;
		}

		$rx = ($widtho > $maxwidth)		? $maxwidth / $widtho	: 1 ;
		$ry = ($heighto > $maxheight)	? $maxheight / $heighto	: 1 ;

		$c = min($rx, $ry);

		$widthr  = $widtho  * $c;
		$heightr = $heighto * $c;

		// modif
		
		if($widtho>=$heighto)
		{
			$widthr = $maxwidth;
			$heightr = ($maxwidth * $heighto) / $widtho;
		}
		else
		{
			$heightr = $maxheight;
			$widthr = ($maxheight * $widtho) / $heighto;
		}
		//echo "nouvelle largeur = ".$widthr."<br>";
		//echo "nouvelle heuteur = ".$heightr."<br>";
		
		//echo "nouvelle largeur :".$widthr;
		
		if(isset($isAvantApres) && $isAvantApres==true)
		{
			$x = floor(($maxwidth  - $widthr ) / 2);
			$y = floor(($maxheight - $heightr) / 2);
			$imgr = imagecreatetruecolor($widthr, $heightr);
			$blue = imagecolorallocatealpha($imgr, 180, 193, 205, 0); 
			imagefill($imgr, 0, 0, $blue);
			imagecopyresized($imgr, $imgo, 0, 0, 0, 0, $widthr, $heightr, $widtho, $heighto);
			
			$color_txt = imagecolorallocatealpha($imgr, 175, 175, 175, 0); 
			imagettftext($imgr, 12, 0, $widthr - 135, $heightr - 3, $color_txt, $font, $txt);

			$imgt = imagecreatetruecolor($widthr, $heightr);	
			$blue = imagecolorallocatealpha($imgt, 180, 193, 205, 0); 
			imagefill($imgt, 0, 0, $blue);
			imagecopyresized($imgt, $imgr, 0, 0, 0, 0, $widthr, $heightr, $widthr, $heightr);
		}
		else
		{
			$x = floor(($maxwidth  - $widthr ) / 2);
			$y = floor(($maxheight - $heightr) / 2);
			
			$imgr = imagecreatetruecolor($widthr, $heightr);
			$blue = imagecolorallocatealpha($imgr, 180, 193, 205, 0); 
			imagefill($imgr, 0, 0, $blue);
			imagecopyresized($imgr, $imgo, 0, 0, 0, 0, $widthr, $heightr, $widtho, $heighto);
			
			$color_txt = imagecolorallocatealpha($imgr, 175, 175, 175, 0); 
			imagettftext($imgr, 12, 0, $widthr - 135, $heightr - 3, $color_txt, $font, $txt);

			$imgt = imagecreatetruecolor($maxwidth, $maxheight);	
			$blue = imagecolorallocatealpha($imgt, 180, 193, 205, 0); 
			imagefill($imgt, 0, 0, $blue);
			imagecopyresized($imgt, $imgr, $x, $y, 0, 0, $widthr, $heightr, $widthr, $heightr);
			
		}
		
		ob_start();
		//header('Content-type: image/jpeg');
		imagejpeg($imgt,$fichierDestination,100);
		ob_end_flush();
		imagedestroy($imgt);
		imagedestroy($imgo);
		imagedestroy($imgr);
	}
	
	// renvoie le code javascript pour la selection d'une zone d'image et recuperation des coordonnées de cette zone
	// a placer apres l'affichage de l'image dans le code
	public function getJsCodeSelectionZone($params=array())
	{
		$html="";
		
		$html.="<input type='hidden' name='x1' id='x1' value=''>";
		$html.="<input type='hidden' name='y1' id='y1' value=''>";
		$html.="<input type='hidden' name='x2' id='x2' value=''>";
		$html.="<input type='hidden' name='y2' id='y2' value=''>";
		
		
		
		//$html.="<input type='text' name='debugX' id='debugX' value=''>";
		
		//$html.="<input type='text' name='debugY' id='debugY' value=''>";
		
		// formulaire auquel on va rajouter des inputs dynamiquement
		$htmlElementForm="";
		if(isset($params['addHTMLElementsToFormValidatedAfterZoneSelection']) && $params['addHTMLElementsToFormValidatedAfterZoneSelection']!='')
		{
			$htmlElementForm = $params['addHTMLElementsToFormValidatedAfterZoneSelection'];
		}
		
		$actionForm ="";
		if(isset($params['actionFormValidateZone']) && $params['actionFormValidateZone']!='')
		{
			$actionForm = $params['actionFormValidateZone'];
		}
		
		
		$html.="<form action='".$actionForm."' id='formRetourArrayPoints' enctype='multipart/form-data' method='POST'>".$htmlElementForm."</form>";
		
		// fonctions
		if(!isset($params['noBalisesJs']) || $params['noBalisesJs']==false)
		{
			$html.="<script  >";
		}
		
		if(isset($params['tracePolygoneResultat']) && $params['tracePolygoneResultat']==true) // les fonctions de tracage doivent avoir ete initialisée avant
		{
			$html.="var tracePolygoneResultat=true;";
		}
		else
		{
			$html.="var tracePolygoneResultat=false;";
		}
		
		$nomIDImage="divImage";
		if(isset($params['nomIDImage']) && $params['nomIDImage']!='')
		{
			$nomIDImage=$params['nomIDImage'];
		}
		
		$tailleMaxRectangleSelection=4000;
		if(isset($params['tailleMaxRectangleDeSelection']) && $params['tailleMaxRectangleDeSelection']!='')
		{
			$tailleMaxRectangleSelection=$params['tailleMaxRectangleDeSelection'];
		}
		
		$html.="
		
			var img;
			var rectangle; // rectangle de selection
			var rectangleStyle;
			var numPoint=0;
			var imgId;
			var arrayPoints = new Array();
			
			
			var x_init=0;
			var y_init=0;
			
			var x_final = 0;
			var y_final = 0;
			
			var rs; // instance du style du div du rectangle de selection
			var s = $tailleMaxRectangleSelection; // taille maximale du rectangle de selection
			
			var positions; // position du div de l'image
			
			var w;
			
			
			function init_frmWorkSelectionZone(imageId)
			{
				img = document.getElementById(imageId);
				img.onmousedown=down; // si click sur l'image
				imgId = imageId;
				positions = findPos(img); // position de l'image , meme pour ie !
				//w = new window.open();
				
			}
						
			function createPointDiv(numPoint)
			{
				newDiv = document.createElement('div');
				newDiv.setAttribute('id','pointSelection'+numPoint);
				newDiv.style.position='absolute';
				document.getElementById('$nomIDImage').appendChild(newDiv);
				return newDiv;
			}
			
			function up(event)
			{
				if(!event) event = window.event;
			}
			
			
			function move(event)
			{
				if(!event) event = window.event;
				
				document.onmousedown=down;
				
				if(document.all)
				{
					x_new = event.offsetX;//?(event.offsetX):event.pageX - img.offsetLeft;
					y_new = event.offsetY;//?(event.offsetY):event.pageY - img.offsetTop;
				}
				else
				{
					x_new = event.pageX - img.offsetLeft;
					y_new = event.pageY - img.offsetTop;
				}
				
				/*dx = document.getElementById('debugX');
				dy = document.getElementById('debugY');
				dx.value='x_init='+x_init+'x_new='+x_new;
				dy.value='y_init='+y_init+'y_new='+y_new;
				*/
				
				if (x_new >= x_init) 
				{ 
					if(x_new - x_init < s) 
					{ 
						rs.marginLeft = x_init+'px'; 
						rs.width = (x_new - x_init)+'px';
					}
				}
				else 
				{ 
					if(x_init - x_new < s) 
					{
						rs.marginLeft = x_new+'px'; 
						rs.width = (x_init - x_new)+'px';
					}
				}
				
				if (y_new >= y_init) 
				{ 
					if(y_new - y_init < s) 
					{
						rs.marginTop = y_init+'px'; 
						rs.height = (y_new - y_init)+'px';
					}
				}
				else 
				{
					if(y_init - y_new < s) 
					{
						rs.marginTop = y_new+'px'; 
						rs.height = (y_init - y_new)+'px';
					}
				}
				

			}
			
			
			
			function down(event)
			{
				if(!event) event = window.event;
				
				if(arrayPoints.length<=0)
				{
					document.onmousemove=move;
					document.onmouseup=up;
					
					if(document.all)
					{
						x_init = event.offsetX;//?(event.offsetX):event.pageX - img.offsetLeft;
						y_init = event.offsetY;//?(event.offsetY):event.pageY - img.offsetTop;
					}
					else
					{
						x_init = event.pageX - img.offsetLeft;
						y_init = event.pageY - img.offsetTop;
					}
					
					
					divRS = document.createElement('div');
					divRS.setAttribute('id','rectangleSelectionDiv');
					
					document.getElementById('$nomIDImage').appendChild(divRS);
					rs = document.getElementById('rectangleSelectionDiv').style;
					
					rs.position=\"absolute\"; 
					rs.left=\"0\"; 
					rs.top=\"0\";
					rs.background=\"#ccd7ff\"; 
					rs.border=\"1px dashed #000\";
					rs.opacity=\"0.40\"; 
					rs.filter=\"alpha(Opacity=40)\";
					rs.visibility=\"visible\"; 
					rs.cursor=\"crosshair\";				
					
				}
				
				// button = 0 pour firefox , button = 1 pour ie (!) ... 
				if(event.button==0 || event.button==1)
				{
					// leftclick
					pos_x = event.offsetX?(event.offsetX):event.pageX - img.offsetLeft;
					pos_y = event.offsetY?(event.offsetY):event.pageY - img.offsetTop;
										
					// point rouge de départ
					newDiv=createPointDiv(numPoint);
					newDiv.style.backgroundColor='#FF0000';

					if(document.all)
					{//IE
						// pour la hauteur minimale dans ie qui est imposée a 10 ou 12 pixel :
						newDiv.style.lineHeight='0';
						newDiv.style.fontSize='0';
					}
					
					newDiv.style.width='5px';
					newDiv.style.height='5px';
					newDiv.style.top=pos_y-2;
					newDiv.style.left=pos_x-2;
					newDiv.style.display='block';
					
					arrayPoints[numPoint]=new Array(pos_x,pos_y);
					
					numPoint++;
					
					document.getElementById('x1').value=pos_x;
					document.getElementById('y1').value=pos_y;
				}
				
				if(arrayPoints.length==2)
				{
					for(i=0;i<arrayPoints.length;i++)
					{
						newInputX = document.createElement('input');
						newInputX.setAttribute('name','inputArrayPointX['+i+']');
						newInputX.setAttribute('value',arrayPoints[i][0]);
						newInputX.setAttribute('type','hidden');
						
						newInputY = document.createElement('input');
						newInputY.setAttribute('name','inputArrayPointY['+i+']');
						newInputY.setAttribute('value',arrayPoints[i][1]);
						newInputY.setAttribute('type','hidden');
						
						document.getElementById('formRetourArrayPoints').appendChild(newInputX);
						document.getElementById('formRetourArrayPoints').appendChild(newInputY);
					}
					rs.visibility=\"hidden\";
					
					if(i>2)
					{
						// avec + de 2 points on fait un polygone
						
						if(tracePolygoneResultat)
						{
							// appel des fonctions de tracage qui doivent avoir ete initialisé avant
							//var jg = new jsGraphics('$nomIDImage');
							// pas de polygone pour le moment
						}
					}
				
					if(i==2)
					{
						// avec 2 points on fait un carré
						
						if(tracePolygoneResultat)
						{
							drawDiv = document.createElement('div'); 
							drawDiv.setAttribute('id','divDraw');
								
							if(document.all)
							{//IE
	drawDiv.setAttribute('style','background-color:#00FF00;position:absolute;top:'+img.offsetTop+'px;left:'+img.offsetLeft+'px;display:block;width:0px;height:0px;');
							}
							else
							{//FF
	drawDiv.setAttribute('style','background-color:#00FF00;position:absolute;top:0px;left:0px;display:block;width:0px;height:0px;');
							}


							document.getElementById('$nomIDImage').appendChild(drawDiv);
							
							
							
							X = arrayPoints[0][0];
							Y = arrayPoints[0][1];
							width = arrayPoints[1][0] - arrayPoints[0][0];
							height = arrayPoints[1][1] - arrayPoints[0][1];

							// on affiche le calque semi transparent qui indique la selection une fois le deuxieme point clique
							
							drawDivAff = document.getElementById('divDraw');
							drawDivAff.style.position='absolute';
							drawDivAff.style.backgroundColor='#0000ff';
							drawDivAff.style.left = X;
							drawDivAff.style.top = Y;
							drawDivAff.style.width = width;
							drawDivAff.style.height = height;
							
							if(document.all)
							{// IE
								set_opacity('divDraw',40);
							}
							else
							{// FF
								drawDivAff.style.opacity = 0.40;
							}
							
							";
							if(isset($params['onZoneSelectedAction']) && $params['onZoneSelectedAction']!='')
							{
								$html.=$params['onZoneSelectedAction'];
							}
							
							$html.="
							
							
							
						}
					}
				}
				

				
				if(event.button==2)
				{
					// rightclick
					
					
				}
			}
			
			function findPos(obj) 
			{
				var curleft,curtop = 0;
				if (obj.offsetParent) {
					curleft = obj.offsetLeft;
					curtop = obj.offsetTop;
					while (obj = obj.offsetParent) {
						curleft += obj.offsetLeft;
						curtop += obj.offsetTop;
					}
				}
				return [curleft,curtop];
			}
		
		";
		
		if(isset($params['nomIDImage']) && $params['nomIDImage']!='')
		{
			$html.="init_frmWorkSelectionZone('".$params['nomIDImage']."')";
		}
		else
		{
			$html.="init_frmWorkSelectionZone('divImage')";
		}
		
		
		
		
		if(!isset($params['noBalisesJs']) || $params['noBalisesJs']==false)
		{
			$html.="</script>";
		}
		
		
		return $html;
	}
	
	
	public function getJsCodeDrawFunctions()
	{
		$html="";
		
		// fonctions
		if(!isset($params['noBalisesJs']) || $params['noBalisesJs']==false)
		{
			$html.="<script  >";
		}
		
		$html.="/* This notice must be untouched at all times.

			wz_jsgraphics.js    v. 3.05
			The latest version is available at
			http://www.walterzorn.com
			or http://www.devira.com
			or http://www.walterzorn.de

			Copyright (c) 2002-2009 Walter Zorn. All rights reserved.
			Created 3. 11. 2002 by Walter Zorn (Web: http://www.walterzorn.com )
			Last modified: 2. 2. 2009

			Performance optimizations for Internet Explorer
			by Thomas Frank and John Holdsworth.
			fillPolygon method implemented by Matthieu Haller.

			High Performance JavaScript Graphics Library.
			Provides methods
			- to draw lines, rectangles, ellipses, polygons
				with specifiable line thickness,
			- to fill rectangles, polygons, ellipses and arcs
			- to draw text.
			NOTE: Operations, functions and branching have rather been optimized
			to efficiency and speed than to shortness of source code.

			LICENSE: LGPL

			This library is free software; you can redistribute it and/or
			modify it under the terms of the GNU Lesser General Public
			License (LGPL) as published by the Free Software Foundation; either
			version 2.1 of the License, or (at your option) any later version.

			This library is distributed in the hope that it will be useful,
			but WITHOUT ANY WARRANTY; without even the implied warranty of
			MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
			Lesser General Public License for more details.

			You should have received a copy of the GNU Lesser General Public
			License along with this library; if not, write to the Free Software
			Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA,
			or see http://www.gnu.org/copyleft/lesser.html
			*/


			var jg_ok, jg_ie, jg_fast, jg_dom, jg_moz;


			function _chkDHTM(wnd, x, i)
			// Under XUL, owner of 'document' must be specified explicitly
			{
				x = wnd.document.body || null;
				jg_ie = x && typeof x.insertAdjacentHTML != \"undefined\" && wnd.document.createElement;
				jg_dom = (x && !jg_ie &&
					typeof x.appendChild != \"undefined\" &&
					typeof wnd.document.createRange != \"undefined\" &&
					typeof (i = wnd.document.createRange()).setStartBefore != \"undefined\" &&
					typeof i.createContextualFragment != \"undefined\");
				jg_fast = jg_ie && wnd.document.all && !wnd.opera;
				jg_moz = jg_dom && typeof x.style.MozOpacity != \"undefined\";
				jg_ok = !!(jg_ie || jg_dom);
			}

			function _pntCnvDom()
			{
				var x = this.wnd.document.createRange();
				x.setStartBefore(this.cnv);
				x = x.createContextualFragment(jg_fast? this._htmRpc() : this.htm);
				if(this.cnv) this.cnv.appendChild(x);
				this.htm = \"\";
			}

			function _pntCnvIe()
			{
				if(this.cnv) this.cnv.insertAdjacentHTML(\"BeforeEnd\", jg_fast? this._htmRpc() : this.htm);
				this.htm = \"\";
			}

			function _pntDoc()
			{
				this.wnd.document.write(jg_fast? this._htmRpc() : this.htm);
				this.htm = '';
			}

			function _pntN()
			{
				;
			}

			function _mkDiv(x, y, w, h)
			{
				this.htm += '<div style=\"position:absolute;'+
					'left:' + x + 'px;'+
					'top:' + y + 'px;'+
					'width:' + w + 'px;'+
					'height:' + h + 'px;'+
					'clip:rect(0,'+w+'px,'+h+'px,0);'+
					'background-color:' + this.color +
					(!jg_moz? ';overflow:hidden' : '')+
					';\"><\/div>';
			}

			function _mkDivIe(x, y, w, h)
			{
				this.htm += '%%'+this.color+';'+x+';'+y+';'+w+';'+h+';';
			}

			function _mkDivPrt(x, y, w, h)
			{
				this.htm += '<div style=\"position:absolute;'+
					'border-left:' + w + 'px solid ' + this.color + ';'+
					'left:' + x + 'px;'+
					'top:' + y + 'px;'+
					'width:0px;'+
					'height:' + h + 'px;'+
					'clip:rect(0,'+w+'px,'+h+'px,0);'+
					'background-color:' + this.color +
					(!jg_moz? ';overflow:hidden' : '')+
					';\"><\/div>';
			}

			var _regex =  /%%([^;]+);([^;]+);([^;]+);([^;]+);([^;]+);/g;
			function _htmRpc()
			{
				return this.htm.replace(
					_regex,
					'<div style=\"overflow:hidden;position:absolute;background-color:'+
					'$1;left:$2px;top:$3px;width:$4px;height:$5px\"></div>');
			}

			function _htmPrtRpc()
			{
				return this.htm.replace(
					_regex,
					'<div style=\"overflow:hidden;position:absolute;background-color:'+
					'$1;left:$2px;top:$3px;width:$4px;height:$5px;border-left:$4px solid $1\"></div>');
			}

			function _mkLin(x1, y1, x2, y2)
			{
				if(x1 > x2)
				{
					var _x2 = x2;
					var _y2 = y2;
					x2 = x1;
					y2 = y1;
					x1 = _x2;
					y1 = _y2;
				}
				var dx = x2-x1, dy = Math.abs(y2-y1),
				x = x1, y = y1,
				yIncr = (y1 > y2)? -1 : 1;

				if(dx >= dy)
				{
					var pr = dy<<1,
					pru = pr - (dx<<1),
					p = pr-dx,
					ox = x;
					while(dx > 0)
					{--dx;
						++x;
						if(p > 0)
						{
							this._mkDiv(ox, y, x-ox, 1);
							y += yIncr;
							p += pru;
							ox = x;
						}
						else p += pr;
					}
					this._mkDiv(ox, y, x2-ox+1, 1);
				}

				else
				{
					var pr = dx<<1,
					pru = pr - (dy<<1),
					p = pr-dy,
					oy = y;
					if(y2 <= y1)
					{
						while(dy > 0)
						{--dy;
							if(p > 0)
							{
								this._mkDiv(x++, y, 1, oy-y+1);
								y += yIncr;
								p += pru;
								oy = y;
							}
							else
							{
								y += yIncr;
								p += pr;
							}
						}
						this._mkDiv(x2, y2, 1, oy-y2+1);
					}
					else
					{
						while(dy > 0)
						{--dy;
							y += yIncr;
							if(p > 0)
							{
								this._mkDiv(x++, oy, 1, y-oy);
								p += pru;
								oy = y;
							}
							else p += pr;
						}
						this._mkDiv(x2, oy, 1, y2-oy+1);
					}
				}
			}

			function _mkLin2D(x1, y1, x2, y2)
			{
				if(x1 > x2)
				{
					var _x2 = x2;
					var _y2 = y2;
					x2 = x1;
					y2 = y1;
					x1 = _x2;
					y1 = _y2;
				}
				var dx = x2-x1, dy = Math.abs(y2-y1),
				x = x1, y = y1,
				yIncr = (y1 > y2)? -1 : 1;

				var s = this.stroke;
				if(dx >= dy)
				{
					if(dx > 0 && s-3 > 0)
					{
						var _s = (s*dx*Math.sqrt(1+dy*dy/(dx*dx))-dx-(s>>1)*dy) / dx;
						_s = (!(s-4)? Math.ceil(_s) : Math.round(_s)) + 1;
					}
					else var _s = s;
					var ad = Math.ceil(s/2);

					var pr = dy<<1,
					pru = pr - (dx<<1),
					p = pr-dx,
					ox = x;
					while(dx > 0)
					{--dx;
						++x;
						if(p > 0)
						{
							this._mkDiv(ox, y, x-ox+ad, _s);
							y += yIncr;
							p += pru;
							ox = x;
						}
						else p += pr;
					}
					this._mkDiv(ox, y, x2-ox+ad+1, _s);
				}

				else
				{
					if(s-3 > 0)
					{
						var _s = (s*dy*Math.sqrt(1+dx*dx/(dy*dy))-(s>>1)*dx-dy) / dy;
						_s = (!(s-4)? Math.ceil(_s) : Math.round(_s)) + 1;
					}
					else var _s = s;
					var ad = Math.round(s/2);

					var pr = dx<<1,
					pru = pr - (dy<<1),
					p = pr-dy,
					oy = y;
					if(y2 <= y1)
					{
						++ad;
						while(dy > 0)
						{--dy;
							if(p > 0)
							{
								this._mkDiv(x++, y, _s, oy-y+ad);
								y += yIncr;
								p += pru;
								oy = y;
							}
							else
							{
								y += yIncr;
								p += pr;
							}
						}
						this._mkDiv(x2, y2, _s, oy-y2+ad);
					}
					else
					{
						while(dy > 0)
						{--dy;
							y += yIncr;
							if(p > 0)
							{
								this._mkDiv(x++, oy, _s, y-oy+ad);
								p += pru;
								oy = y;
							}
							else p += pr;
						}
						this._mkDiv(x2, oy, _s, y2-oy+ad+1);
					}
				}
			}

			function _mkLinDott(x1, y1, x2, y2)
			{
				if(x1 > x2)
				{
					var _x2 = x2;
					var _y2 = y2;
					x2 = x1;
					y2 = y1;
					x1 = _x2;
					y1 = _y2;
				}
				var dx = x2-x1, dy = Math.abs(y2-y1),
				x = x1, y = y1,
				yIncr = (y1 > y2)? -1 : 1,
				drw = true;
				if(dx >= dy)
				{
					var pr = dy<<1,
					pru = pr - (dx<<1),
					p = pr-dx;
					while(dx > 0)
					{--dx;
						if(drw) this._mkDiv(x, y, 1, 1);
						drw = !drw;
						if(p > 0)
						{
							y += yIncr;
							p += pru;
						}
						else p += pr;
						++x;
					}
				}
				else
				{
					var pr = dx<<1,
					pru = pr - (dy<<1),
					p = pr-dy;
					while(dy > 0)
					{--dy;
						if(drw) this._mkDiv(x, y, 1, 1);
						drw = !drw;
						y += yIncr;
						if(p > 0)
						{
							++x;
							p += pru;
						}
						else p += pr;
					}
				}
				if(drw) this._mkDiv(x, y, 1, 1);
			}

			function _mkOv(left, top, width, height)
			{
				var a = (++width)>>1, b = (++height)>>1,
				wod = width&1, hod = height&1,
				cx = left+a, cy = top+b,
				x = 0, y = b,
				ox = 0, oy = b,
				aa2 = (a*a)<<1, aa4 = aa2<<1, bb2 = (b*b)<<1, bb4 = bb2<<1,
				st = (aa2>>1)*(1-(b<<1)) + bb2,
				tt = (bb2>>1) - aa2*((b<<1)-1),
				w, h;
				while(y > 0)
				{
					if(st < 0)
					{
						st += bb2*((x<<1)+3);
						tt += bb4*(++x);
					}
					else if(tt < 0)
					{
						st += bb2*((x<<1)+3) - aa4*(y-1);
						tt += bb4*(++x) - aa2*(((y--)<<1)-3);
						w = x-ox;
						h = oy-y;
						if((w&2) && (h&2))
						{
							this._mkOvQds(cx, cy, x-2, y+2, 1, 1, wod, hod);
							this._mkOvQds(cx, cy, x-1, y+1, 1, 1, wod, hod);
						}
						else this._mkOvQds(cx, cy, x-1, oy, w, h, wod, hod);
						ox = x;
						oy = y;
					}
					else
					{
						tt -= aa2*((y<<1)-3);
						st -= aa4*(--y);
					}
				}
				w = a-ox+1;
				h = (oy<<1)+hod;
				y = cy-oy;
				this._mkDiv(cx-a, y, w, h);
				this._mkDiv(cx+ox+wod-1, y, w, h);
			}

			function _mkOv2D(left, top, width, height)
			{
				var s = this.stroke;
				width += s+1;
				height += s+1;
				var a = width>>1, b = height>>1,
				wod = width&1, hod = height&1,
				cx = left+a, cy = top+b,
				x = 0, y = b,
				aa2 = (a*a)<<1, aa4 = aa2<<1, bb2 = (b*b)<<1, bb4 = bb2<<1,
				st = (aa2>>1)*(1-(b<<1)) + bb2,
				tt = (bb2>>1) - aa2*((b<<1)-1);

				if(s-4 < 0 && (!(s-2) || width-51 > 0 && height-51 > 0))
				{
					var ox = 0, oy = b,
					w, h,
					pxw;
					while(y > 0)
					{
						if(st < 0)
						{
							st += bb2*((x<<1)+3);
							tt += bb4*(++x);
						}
						else if(tt < 0)
						{
							st += bb2*((x<<1)+3) - aa4*(y-1);
							tt += bb4*(++x) - aa2*(((y--)<<1)-3);
							w = x-ox;
							h = oy-y;

							if(w-1)
							{
								pxw = w+1+(s&1);
								h = s;
							}
							else if(h-1)
							{
								pxw = s;
								h += 1+(s&1);
							}
							else pxw = h = s;
							this._mkOvQds(cx, cy, x-1, oy, pxw, h, wod, hod);
							ox = x;
							oy = y;
						}
						else
						{
							tt -= aa2*((y<<1)-3);
							st -= aa4*(--y);
						}
					}
					this._mkDiv(cx-a, cy-oy, s, (oy<<1)+hod);
					this._mkDiv(cx+a+wod-s, cy-oy, s, (oy<<1)+hod);
				}

				else
				{
					var _a = (width-(s<<1))>>1,
					_b = (height-(s<<1))>>1,
					_x = 0, _y = _b,
					_aa2 = (_a*_a)<<1, _aa4 = _aa2<<1, _bb2 = (_b*_b)<<1, _bb4 = _bb2<<1,
					_st = (_aa2>>1)*(1-(_b<<1)) + _bb2,
					_tt = (_bb2>>1) - _aa2*((_b<<1)-1),

					pxl = new Array(),
					pxt = new Array(),
					_pxb = new Array();
					pxl[0] = 0;
					pxt[0] = b;
					_pxb[0] = _b-1;
					while(y > 0)
					{
						if(st < 0)
						{
							pxl[pxl.length] = x;
							pxt[pxt.length] = y;
							st += bb2*((x<<1)+3);
							tt += bb4*(++x);
						}
						else if(tt < 0)
						{
							pxl[pxl.length] = x;
							st += bb2*((x<<1)+3) - aa4*(y-1);
							tt += bb4*(++x) - aa2*(((y--)<<1)-3);
							pxt[pxt.length] = y;
						}
						else
						{
							tt -= aa2*((y<<1)-3);
							st -= aa4*(--y);
						}

						if(_y > 0)
						{
							if(_st < 0)
							{
								_st += _bb2*((_x<<1)+3);
								_tt += _bb4*(++_x);
								_pxb[_pxb.length] = _y-1;
							}
							else if(_tt < 0)
							{
								_st += _bb2*((_x<<1)+3) - _aa4*(_y-1);
								_tt += _bb4*(++_x) - _aa2*(((_y--)<<1)-3);
								_pxb[_pxb.length] = _y-1;
							}
							else
							{
								_tt -= _aa2*((_y<<1)-3);
								_st -= _aa4*(--_y);
								_pxb[_pxb.length-1]--;
							}
						}
					}

					var ox = -wod, oy = b,
					_oy = _pxb[0],
					l = pxl.length,
					w, h;
					for(var i = 0; i < l; i++)
					{
						if(typeof _pxb[i] != \"undefined\")
						{
							if(_pxb[i] < _oy || pxt[i] < oy)
							{
								x = pxl[i];
								this._mkOvQds(cx, cy, x, oy, x-ox, oy-_oy, wod, hod);
								ox = x;
								oy = pxt[i];
								_oy = _pxb[i];
							}
						}
						else
						{
							x = pxl[i];
							this._mkDiv(cx-x, cy-oy, 1, (oy<<1)+hod);
							this._mkDiv(cx+ox+wod, cy-oy, 1, (oy<<1)+hod);
							ox = x;
							oy = pxt[i];
						}
					}
					this._mkDiv(cx-a, cy-oy, 1, (oy<<1)+hod);
					this._mkDiv(cx+ox+wod, cy-oy, 1, (oy<<1)+hod);
				}
			}

			function _mkOvDott(left, top, width, height)
			{
				var a = (++width)>>1, b = (++height)>>1,
				wod = width&1, hod = height&1, hodu = hod^1,
				cx = left+a, cy = top+b,
				x = 0, y = b,
				aa2 = (a*a)<<1, aa4 = aa2<<1, bb2 = (b*b)<<1, bb4 = bb2<<1,
				st = (aa2>>1)*(1-(b<<1)) + bb2,
				tt = (bb2>>1) - aa2*((b<<1)-1),
				drw = true;
				while(y > 0)
				{
					if(st < 0)
					{
						st += bb2*((x<<1)+3);
						tt += bb4*(++x);
					}
					else if(tt < 0)
					{
						st += bb2*((x<<1)+3) - aa4*(y-1);
						tt += bb4*(++x) - aa2*(((y--)<<1)-3);
					}
					else
					{
						tt -= aa2*((y<<1)-3);
						st -= aa4*(--y);
					}
					if(drw && y >= hodu) this._mkOvQds(cx, cy, x, y, 1, 1, wod, hod);
					drw = !drw;
				}
			}

			function _mkRect(x, y, w, h)
			{
				var s = this.stroke;
				this._mkDiv(x, y, w, s);
				this._mkDiv(x+w, y, s, h);
				this._mkDiv(x, y+h, w+s, s);
				this._mkDiv(x, y+s, s, h-s);
			}

			function _mkRectDott(x, y, w, h)
			{
				this.drawLine(x, y, x+w, y);
				this.drawLine(x+w, y, x+w, y+h);
				this.drawLine(x, y+h, x+w, y+h);
				this.drawLine(x, y, x, y+h);
			}

			function jsgFont()
			{
				this.PLAIN = 'font-weight:normal;';
				this.BOLD = 'font-weight:bold;';
				this.ITALIC = 'font-style:italic;';
				this.ITALIC_BOLD = this.ITALIC + this.BOLD;
				this.BOLD_ITALIC = this.ITALIC_BOLD;
			}
			var Font = new jsgFont();

			function jsgStroke()
			{
				this.DOTTED = -1;
			}
			var Stroke = new jsgStroke();

			function jsGraphics(cnv, wnd)
			{
				this.setColor = function(x)
				{
					this.color = x.toLowerCase();
				};

				this.setStroke = function(x)
				{
					this.stroke = x;
					if(!(x+1))
					{
						this.drawLine = _mkLinDott;
						this._mkOv = _mkOvDott;
						this.drawRect = _mkRectDott;
					}
					else if(x-1 > 0)
					{
						this.drawLine = _mkLin2D;
						this._mkOv = _mkOv2D;
						this.drawRect = _mkRect;
					}
					else
					{
						this.drawLine = _mkLin;
						this._mkOv = _mkOv;
						this.drawRect = _mkRect;
					}
				};

				this.setPrintable = function(arg)
				{
					this.printable = arg;
					if(jg_fast)
					{
						this._mkDiv = _mkDivIe;
						this._htmRpc = arg? _htmPrtRpc : _htmRpc;
					}
					else this._mkDiv = arg? _mkDivPrt : _mkDiv;
				};

				this.setFont = function(fam, sz, sty)
				{
					this.ftFam = fam;
					this.ftSz = sz;
					this.ftSty = sty || Font.PLAIN;
				};

				this.drawPolyline = this.drawPolyLine = function(x, y)
				{
					for (var i=x.length - 1; i;)
					{--i;
						this.drawLine(x[i], y[i], x[i+1], y[i+1]);
					}
				};

				this.fillRect = function(x, y, w, h)
				{
					this._mkDiv(x, y, w, h);
				};

				this.drawPolygon = function(x, y)
				{
					this.drawPolyline(x, y);
					this.drawLine(x[x.length-1], y[x.length-1], x[0], y[0]);
				};

				this.drawEllipse = this.drawOval = function(x, y, w, h)
				{
					this._mkOv(x, y, w, h);
				};

				this.fillEllipse = this.fillOval = function(left, top, w, h)
				{
					var a = w>>1, b = h>>1,
					wod = w&1, hod = h&1,
					cx = left+a, cy = top+b,
					x = 0, y = b, oy = b,
					aa2 = (a*a)<<1, aa4 = aa2<<1, bb2 = (b*b)<<1, bb4 = bb2<<1,
					st = (aa2>>1)*(1-(b<<1)) + bb2,
					tt = (bb2>>1) - aa2*((b<<1)-1),
					xl, dw, dh;
					if(w) while(y > 0)
					{
						if(st < 0)
						{
							st += bb2*((x<<1)+3);
							tt += bb4*(++x);
						}
						else if(tt < 0)
						{
							st += bb2*((x<<1)+3) - aa4*(y-1);
							xl = cx-x;
							dw = (x<<1)+wod;
							tt += bb4*(++x) - aa2*(((y--)<<1)-3);
							dh = oy-y;
							this._mkDiv(xl, cy-oy, dw, dh);
							this._mkDiv(xl, cy+y+hod, dw, dh);
							oy = y;
						}
						else
						{
							tt -= aa2*((y<<1)-3);
							st -= aa4*(--y);
						}
					}
					this._mkDiv(cx-a, cy-oy, w, (oy<<1)+hod);
				};

				this.fillArc = function(iL, iT, iW, iH, fAngA, fAngZ)
				{
					var a = iW>>1, b = iH>>1,
					iOdds = (iW&1) | ((iH&1) << 16),
					cx = iL+a, cy = iT+b,
					x = 0, y = b, ox = x, oy = y,
					aa2 = (a*a)<<1, aa4 = aa2<<1, bb2 = (b*b)<<1, bb4 = bb2<<1,
					st = (aa2>>1)*(1-(b<<1)) + bb2,
					tt = (bb2>>1) - aa2*((b<<1)-1),
					// Vars for radial boundary lines
					xEndA, yEndA, xEndZ, yEndZ,
					iSects = (1 << (Math.floor((fAngA %= 360.0)/180.0) << 3))
							| (2 << (Math.floor((fAngZ %= 360.0)/180.0) << 3))
							| ((fAngA >= fAngZ) << 16),
					aBndA = new Array(b+1), aBndZ = new Array(b+1);
					
					// Set up radial boundary lines
					fAngA *= Math.PI/180.0;
					fAngZ *= Math.PI/180.0;
					xEndA = cx+Math.round(a*Math.cos(fAngA));
					yEndA = cy+Math.round(-b*Math.sin(fAngA));
					_mkLinVirt(aBndA, cx, cy, xEndA, yEndA);
					xEndZ = cx+Math.round(a*Math.cos(fAngZ));
					yEndZ = cy+Math.round(-b*Math.sin(fAngZ));
					_mkLinVirt(aBndZ, cx, cy, xEndZ, yEndZ);

					while(y > 0)
					{
						if(st < 0) // Advance x
						{
							st += bb2*((x<<1)+3);
							tt += bb4*(++x);
						}
						else if(tt < 0) // Advance x and y
						{
							st += bb2*((x<<1)+3) - aa4*(y-1);
							ox = x;
							tt += bb4*(++x) - aa2*(((y--)<<1)-3);
							this._mkArcDiv(ox, y, oy, cx, cy, iOdds, aBndA, aBndZ, iSects);
							oy = y;
						}
						else // Advance y
						{
							tt -= aa2*((y<<1)-3);
							st -= aa4*(--y);
							if(y && (aBndA[y] != aBndA[y-1] || aBndZ[y] != aBndZ[y-1]))
							{
								this._mkArcDiv(x, y, oy, cx, cy, iOdds, aBndA, aBndZ, iSects);
								ox = x;
								oy = y;
							}
						}
					}
					this._mkArcDiv(x, 0, oy, cx, cy, iOdds, aBndA, aBndZ, iSects);
					if(iOdds >> 16) // Odd height
					{
						if(iSects >> 16) // Start-angle > end-angle
						{
							var xl = (yEndA <= cy || yEndZ > cy)? (cx - x) : cx;
							this._mkDiv(xl, cy, x + cx - xl + (iOdds & 0xffff), 1);
						}
						else if((iSects & 0x01) && yEndZ > cy)
							this._mkDiv(cx - x, cy, x, 1);
					}
				};

			/* fillPolygon method, implemented by Matthieu Haller.
			This javascript function is an adaptation of the gdImageFilledPolygon for Walter Zorn lib.
			C source of GD 1.8.4 found at http://www.boutell.com/gd/

			THANKS to Kirsten Schulz for the polygon fixes!

			The intersection finding technique of this code could be improved
			by remembering the previous intertersection, and by using the slope.
			That could help to adjust intersections to produce a nice
			interior_extrema. */
				this.fillPolygon = function(array_x, array_y)
				{
					var i;
					var y;
					var miny, maxy;
					var x1, y1;
					var x2, y2;
					var ind1, ind2;
					var ints;

					var n = array_x.length;
					if(!n) return;

					miny = array_y[0];
					maxy = array_y[0];
					for(i = 1; i < n; i++)
					{
						if(array_y[i] < miny)
							miny = array_y[i];

						if(array_y[i] > maxy)
							maxy = array_y[i];
					}
					for(y = miny; y <= maxy; y++)
					{
						var polyInts = new Array();
						ints = 0;
						for(i = 0; i < n; i++)
						{
							if(!i)
							{
								ind1 = n-1;
								ind2 = 0;
							}
							else
							{
								ind1 = i-1;
								ind2 = i;
							}
							y1 = array_y[ind1];
							y2 = array_y[ind2];
							if(y1 < y2)
							{
								x1 = array_x[ind1];
								x2 = array_x[ind2];
							}
							else if(y1 > y2)
							{
								y2 = array_y[ind1];
								y1 = array_y[ind2];
								x2 = array_x[ind1];
								x1 = array_x[ind2];
							}
							else continue;

							 //  Modified 11. 2. 2004 Walter Zorn
							if((y >= y1) && (y < y2))
								polyInts[ints++] = Math.round((y-y1) * (x2-x1) / (y2-y1) + x1);

							else if((y == maxy) && (y > y1) && (y <= y2))
								polyInts[ints++] = Math.round((y-y1) * (x2-x1) / (y2-y1) + x1);
						}
						polyInts.sort(_CompInt);
						for(i = 0; i < ints; i+=2)
							this._mkDiv(polyInts[i], y, polyInts[i+1]-polyInts[i]+1, 1);
					}
				};

				this.drawString = function(txt, x, y)
				{
					this.htm += '<div style=\"position:absolute;white-space:nowrap;'+
						'left:' + x + 'px;'+
						'top:' + y + 'px;'+
						'font-family:' +  this.ftFam + ';'+
						'font-size:' + this.ftSz + ';'+
						'color:' + this.color + ';' + this.ftSty + '\">'+
						txt +
						'<\/div>';
				};

			/* drawStringRect() added by Rick Blommers.
			Allows to specify the size of the text rectangle and to align the
			text both horizontally (e.g. right) and vertically within that rectangle */
				this.drawStringRect = function(txt, x, y, width, halign)
				{
					this.htm += '<div style=\"position:absolute;overflow:hidden;'+
						'left:' + x + 'px;'+
						'top:' + y + 'px;'+
						'width:'+width +'px;'+
						'text-align:'+halign+';'+
						'font-family:' +  this.ftFam + ';'+
						'font-size:' + this.ftSz + ';'+
						'color:' + this.color + ';' + this.ftSty + '\">'+
						txt +
						'<\/div>';
				};

				this.drawImage = function(imgSrc, x, y, w, h, a)
				{
					this.htm += '<div style=\"position:absolute;'+
						'left:' + x + 'px;'+
						'top:' + y + 'px;'+
						// w (width) and h (height) arguments are now optional.
						// Added by Mahmut Keygubatli, 14.1.2008
						(w? ('width:' +  w + 'px;') : '') +
						(h? ('height:' + h + 'px;'):'')+'\">'+
						'<img src=\"' + imgSrc +'\"'+ (w ? (' width=\"' + w + '\"'):'')+ (h ? (' height=\"' + h + '\"'):'') + (a? (' '+a) : '') + '>'+
						'<\/div>';
				};

				this.clear = function()
				{
					this.htm = \"\";
					if(this.cnv) this.cnv.innerHTML = \"\";
				};

				this._mkOvQds = function(cx, cy, x, y, w, h, wod, hod)
				{
					var xl = cx - x, xr = cx + x + wod - w, yt = cy - y, yb = cy + y + hod - h;
					if(xr > xl+w)
					{
						this._mkDiv(xr, yt, w, h);
						this._mkDiv(xr, yb, w, h);
					}
					else
						w = xr - xl + w;
					this._mkDiv(xl, yt, w, h);
					this._mkDiv(xl, yb, w, h);
				};
				
				this._mkArcDiv = function(x, y, oy, cx, cy, iOdds, aBndA, aBndZ, iSects)
				{
					var xrDef = cx + x + (iOdds & 0xffff), y2, h = oy - y, xl, xr, w;

					if(!h) h = 1;
					x = cx - x;

					if(iSects & 0xff0000) // Start-angle > end-angle
					{
						y2 = cy - y - h;
						if(iSects & 0x00ff)
						{
							if(iSects & 0x02)
							{
								xl = Math.max(x, aBndZ[y]);
								w = xrDef - xl;
								if(w > 0) this._mkDiv(xl, y2, w, h);
							}
							if(iSects & 0x01)
							{
								xr = Math.min(xrDef, aBndA[y]);
								w = xr - x;
								if(w > 0) this._mkDiv(x, y2, w, h);
							}
						}
						else
							this._mkDiv(x, y2, xrDef - x, h);
						y2 = cy + y + (iOdds >> 16);
						if(iSects & 0xff00)
						{
							if(iSects & 0x0100)
							{
								xl = Math.max(x, aBndA[y]);
								w = xrDef - xl;
								if(w > 0) this._mkDiv(xl, y2, w, h);
							}
							if(iSects & 0x0200)
							{
								xr = Math.min(xrDef, aBndZ[y]);
								w = xr - x;
								if(w > 0) this._mkDiv(x, y2, w, h);
							}
						}
						else
							this._mkDiv(x, y2, xrDef - x, h);
					}
					else
					{
						if(iSects & 0x00ff)
						{
							if(iSects & 0x02)
								xl = Math.max(x, aBndZ[y]);
							else
								xl = x;
							if(iSects & 0x01)
								xr = Math.min(xrDef, aBndA[y]);
							else
								xr = xrDef;
							y2 = cy - y - h;
							w = xr - xl;
							if(w > 0) this._mkDiv(xl, y2, w, h);
						}
						if(iSects & 0xff00)
						{
							if(iSects & 0x0100)
								xl = Math.max(x, aBndA[y]);
							else
								xl = x;
							if(iSects & 0x0200)
								xr = Math.min(xrDef, aBndZ[y]);
							else
								xr = xrDef;
							y2 = cy + y + (iOdds >> 16);
							w = xr - xl;
							if(w > 0) this._mkDiv(xl, y2, w, h);
						}
					}
				};

				this.setStroke(1);
				this.setFont(\"verdana,geneva,helvetica,sans-serif\", \"12px\", Font.PLAIN);
				this.color = \"#000000\";
				this.htm = \"\";
				this.wnd = wnd || window;

				if(!jg_ok) _chkDHTM(this.wnd);
				if(jg_ok)
				{
					if(cnv)
					{
						if(typeof(cnv) == \"string\")
							this.cont = document.all? (this.wnd.document.all[cnv] || null)
								: document.getElementById? (this.wnd.document.getElementById(cnv) || null)
								: null;
						else if(cnv == window.document)
							this.cont = document.getElementsByTagName(\"body\")[0];
						// If cnv is a direct reference to a canvas DOM node
						// (option suggested by Andreas Luleich)
						else this.cont = cnv;
						// Create new canvas inside container DIV. Thus the drawing and clearing
						// methods won't interfere with the container's inner html.
						// Solution suggested by Vladimir.
						this.cnv = this.wnd.document.createElement(\"div\");
						this.cnv.style.fontSize=0;
						this.cont.appendChild(this.cnv);
						this.paint = jg_dom? _pntCnvDom : _pntCnvIe;
					}
					else
						this.paint = _pntDoc;
				}
				else
					this.paint = _pntN;

				this.setPrintable(false);
			}

			function _mkLinVirt(aLin, x1, y1, x2, y2)
			{
				var dx = Math.abs(x2-x1), dy = Math.abs(y2-y1),
				x = x1, y = y1,
				xIncr = (x1 > x2)? -1 : 1,
				yIncr = (y1 > y2)? -1 : 1,
				p,
				i = 0;
				if(dx >= dy)
				{
					var pr = dy<<1,
					pru = pr - (dx<<1);
					p = pr-dx;
					while(dx > 0)
					{--dx;
						if(p > 0)    //  Increment y
						{
							aLin[i++] = x;
							y += yIncr;
							p += pru;
						}
						else p += pr;
						x += xIncr;
					}
				}
				else
				{
					var pr = dx<<1,
					pru = pr - (dy<<1);
					p = pr-dy;
					while(dy > 0)
					{--dy;
						y += yIncr;
						aLin[i++] = x;
						if(p > 0)    //  Increment x
						{
							x += xIncr;
							p += pru;
						}
						else p += pr;
					}
				}
				for(var len = aLin.length, i = len-i; i;)
					aLin[len-(i--)] = x;
			};

			function _CompInt(x, y)
			{
				return(x - y);
			}";
		
		
		if(!isset($params['noBalisesJs']) || $params['noBalisesJs']==false)
		{
			$html.="</script>";
		}
		
		return $html;
	}
	
	public function getJsSetOpacityFunction($params=array())
	{
		$html="";
		if(!isset($params['noBalisesJs']) || $params['noBalisesJs']==false)
		{
			$html.="<script  >";
		}
		
		$html.="	
			function set_opacity(id, opacity)
			{
				el = document.getElementById(id);
				el.style[\"filter\"] = \"alpha(opacity=\"+opacity+\")\";
				el.style[\"-moz-opacity\"] = opacity/100;
				el.style[\"-khtml-opacity\"] = opacity/100;
				el.style[\"opacity\"] = opacity/100;
				return true;
			}";
		if(!isset($params['noBalisesJs']) || $params['noBalisesJs']==false)
		{
			$html.="</script>";
		}
		return $html;
	}
	
}
?>