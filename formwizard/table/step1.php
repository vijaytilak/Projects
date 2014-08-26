<table id="framematerialtable" class="pure-table" width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <th><label for="nooffrontposts">No. of Front Posts</label></th>
    <th colspan="2"><label for="suffitwidth">Left ( Suffit Width ) Right</label></th>
    <th><label for="archmaterial">Arch Material</label></th>
    <th><label for="frontrailmaterial">Front Rail Material</label></th>
    <th><label for="backrailmaterial">Back Rail Material</label></th>
    <th>&nbsp;</th>
  </tr>
  <tr>
    <td><input class="pure-field" name="nooffrontposts" id="nooffrontposts" style="width:100px;padding-left:10px" /></td>
    <td><input class="pure-field" name="leftsuffitwidth" id="leftsuffitwidth" style="width:50px;padding-left:10px" value='0' /></td>
    <td><input class="pure-field" name="rightsuffitwidth" id="rightsuffitwidth" style="width:50px;padding-left:10px" value='0' /></td>
    <td><select name="archmaterial" id="archmaterial"></select></td>
    <td><input type="hidden" name="frontrailmaterial" id="frontrailmaterial" /></td>
    <td><input type="hidden" name="backrailmaterial" id="backrailmaterial" /></td>
    <td><button style="margin:0px 5px 0px 0px;height:28px; position:relative display:inline-block" class="mybutton" type="button" id="UseRecommendedButton">Autopopulate Recommended Values</button></td>
  </tr>
</table>

    <hr />

    <input style="float:left; width:80px" type="text" name="plength1" id="plength1" placeholder="Length" />
    <input style="float:left; width:80px" type="text" name="pheight1" id="pheight1" placeholder="Height"  />
    <div style="float:left" id="supporttype1"></div>
    <div style="float:left" id="postposition1"></div>
    <input style="float:left; width:100px" type="text" name="brackets1" id="brackets1" placeholder="No.of Brackets" />
    <input style="float:left; width:120px" type="text" name="bracketspacing1" id="bracketspacing1" placeholder="Bracket Spacing" />
    <div style="float:left" id="sectiontype1"></div>
    <div style="float:left" id="filltype1"></div>
    <div style="float:left" id="fabrictype1"></div>
    <select id="brackettype1" name="brackettype1">
        <option value=""></option>
        <option value="0">n/a</option>
        <option value="1">br1</option>
        <option value="2">br2</option>
        <option value="3">br3</option>
        <option value="4">br4</option>
    </select>
    <button style="float:left;height:28px; " class="mybutton" type="button" id="UpdateButton">Update</button>
    <br>

    <hr />
    <div id="MessageContainer" style="width: 100%; background:#ffe701; color:#333"></div>
    <div id="FrontTableContainer" style="width: 100%;"></div>
    <input type="hidden" name="quoteaction" value="servervalidatestep1" id="quoteaction" />
    <input type="hidden" name="quoteid" value="<?php echo $quoteid; ?>" id="quoteid" />
