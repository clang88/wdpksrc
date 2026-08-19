MKSAPKG = mksapkg

dir = ./build/persistent-ssh

all:
	$(RM) -r $(dir)
	mkdir -p $(dir)
	git --work-tree=$(dir) checkout master .
	cd $(dir); $(MKSAPKG) -E -s -m MyCloudEX2Ultra
